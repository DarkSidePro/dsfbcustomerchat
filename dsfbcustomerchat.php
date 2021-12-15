<?php
/**
* 2007-2019 PrestaShop.
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Dsfbcustomerchat extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'dsfbcustomerchat';
        $this->tab = 'advertising_marketing';
        $this->version = '1.1.0';
        $this->author = 'Dark-Side.pro';
        $this->need_instance = 1;
        $this->module_key = 'b47ffa5f382e88d449adc62270b0a46f';

        /*
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('DS: Facebook Customer Chat');
        $this->description = $this->l('Module add facebook messenger to chat');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    private function createTab()
    {
        $response = true;
        $parentTabID = Tab::getIdFromClassName('AdminDarkSideMenu');
        if ($parentTabID) {
            $parentTab = new Tab($parentTabID);
        } else {
            $parentTab = new Tab();
            $parentTab->active = 1;
            $parentTab->name = array();
            $parentTab->class_name = 'AdminDarkSideMenu';
            foreach (Language::getLanguages() as $lang) {
                $parentTab->name[$lang['id_lang']] = 'Dark-Side.pro';
            }
            $parentTab->id_parent = 0;
            $parentTab->module = '';
            $response &= $parentTab->add();
        }
        $parentTab_2ID = Tab::getIdFromClassName('AdminDarkSideMenuSecond');
        if ($parentTab_2ID) {
            $parentTab_2 = new Tab($parentTab_2ID);
        } else {
            $parentTab_2 = new Tab();
            $parentTab_2->active = 1;
            $parentTab_2->name = array();
            $parentTab_2->class_name = 'AdminDarkSideMenuSecond';
            foreach (Language::getLanguages() as $lang) {
                $parentTab_2->name[$lang['id_lang']] = 'Dark-Side Config';
            }
            $parentTab_2->id_parent = $parentTab->id;
            $parentTab_2->module = '';
            $response &= $parentTab_2->add();
        }
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdministratorFacebookChat';
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = 'Facebook Chat';
        }
        $tab->id_parent = $parentTab_2->id;
        $tab->module = $this->name;
        $response &= $tab->add();

        return $response;
    }

    private function tabRem()
    {
        $id_tab = Tab::getIdFromClassName('AdministratorFacebookChat');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        $parentTab_2ID = Tab::getIdFromClassName('AdminDarkSideMenuSecond');
        if ($parentTab_2ID) {
            $tabCount_2 = Tab::getNbTabs($parentTab_2ID);
            if ($tabCount_2 == 0) {
                $parentTab_2 = new Tab($parentTab_2ID);
                $parentTab_2->delete();
            }
        }
        $parentTabID = Tab::getIdFromClassName('AdminDarkSideMenu');
        if ($parentTabID) {
            $tabCount = Tab::getNbTabs($parentTabID);
            if ($tabCount == 0) {
                $parentTab = new Tab($parentTabID);
                $parentTab->delete();
            }
        }

        return true;
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update.
     */
    public function install()
    {
        $this->createTab();
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayFooter');
    }

    public function uninstall()
    {
        $this->tabRem();

        return parent::uninstall();
    }

    /**
     * Load the configuration form.
     */
    public function getContent()
    {
        /*
         * If values have been submitted in the form, process.
         */
        if (((bool) Tools::isSubmit('submitFbcustomerchatModule')) == true) {
            $msg = $this->postProcess();

            return $msg.$this->renderForm();
        }

        return $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFbcustomerchatModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'radio',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Select messenger position'),
                        'name' => 'ds_Fbcustomerchat_POSITION',
                        'label' => $this->l('Messenger Position'),
                        'values' => array(
                            array(
                                'id' => 'left',
                                'value' => 0,
                                'label' => $this->getTranslator()->trans(
                                    'Left',
                                    array(),
                                    'Modules.ds_Fbcustomerchat.Admin'
                                ),
                            ),
                            array(
                                'id' => 'right',
                                'value' => 1,
                                'label' => $this->getTranslator()->trans(
                                    'Right',
                                    array(),
                                    'Modules.ds_Fbcustomerchat.Admin'
                                ),
                            ),
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid facebook page id'),
                        'name' => 'ds_Fbcustomerchat_FB',
                        'label' => $this->l('Facebook Page ID'),
                    ),
                    array(
                        'type' => 'color',
                        'col' => 3,
                        'name' => 'ds_Fbcustomerchat_COLOR',
                        'label' => $this->l('Messenger color'),
                    ),
                    array(
                        'type' => 'text',
                        'col' => 3,
                        'name' => 'ds_Fbcustomerchat_GREETINGS',
                        'label' => $this->l('First info when user is logged in Facebook'),
                    ),
                    array(
                        'type' => 'text',
                        'col' => 3,
                        'name' => 'ds_Fbcustomerchat_LOGGEDOUT',
                        'label' => $this->l('First info when user isnt looged in Facebook'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'ds_Fbcustomerchat_FB' => Configuration::get('ds_Fbcustomerchat_FB', null),
            'ds_Fbcustomerchat_COLOR' => Configuration::get('ds_Fbcustomerchat_COLOR', null),
            'ds_Fbcustomerchat_POSITION' => Configuration::get('ds_Fbcustomerchat_POSITION', null),
            'ds_Fbcustomerchat_GREETINGS' => Configuration::get('ds_Fbcustomerchat_GREETINGS', null),
            'ds_Fbcustomerchat_LOGGEDOUT' => Configuration::get('ds_Fbcustomerchat_LOGGEDOUT', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $facebookPageId = Tools::getValue('ds_Fbcustomerchat_FB');
        $color = Tools::getValue('ds_Fbcustomerchat_COLOR');
        $position = Tools::getValue('ds_Fbcustomerchat_POSITION');
        $greetings = Tools::getValue('ds_Fbcustomerchat_GREETINGS');
        $loggedout = Tools::getValue('ds_Fbcustomerchat_LOGGEDOUT');

        $msg = '';

        if (!Validate::isInt($facebookPageId) == true) {
            $msg .= $this->displayError($this->trans('You must correct fill facebook page id', array(), 'Modules.dsfbcustomerchat.Admin'));
        } else {
            if (Configuration::updateValue('ds_Fbcustomerchat_FB', (int) $facebookPageId)) {
                $msg .= $this->displayConfirmation($this->trans('Facebook page id updated successfully.', array(), 'Admin.Dsdeliveryhours.Success'));
            } else {
                $msg .= $this->displayError($this->trans('Facebook page id updating error', array(), 'Modules.dsfbcustomerchat.Admin'));
            }
        }

        if (!Validate::isColor($color) == true) {
            $msg .= $this->displayError($this->trans('You must correct fill color field', array(), 'Modules.dsfbcustomerchat.Admin'));
        } else {
            if (Configuration::updateValue('ds_Fbcustomerchat_COLOR', $color)) {
                $msg .= $this->displayConfirmation($this->trans('Color field updated successfully.', array(), 'Admin.Dsdeliveryhours.Success'));
            } else {
                $msg .= $this->displayError($this->trans('Color field updating error', array(), 'Modules.dsfbcustomerchat.Admin'));
            }
        }

        if (!Validate::isInt($position) == true) {
            $msg .= $this->displayError($this->trans('Messenger position must be a number', array(), 'Modules.dsfbcustomerchat.Admin'));
        } else {
            if (Configuration::updateValue('ds_Fbcustomerchat_POSITION', (int) $position)) {
                $msg .= $this->displayConfirmation($this->trans('Messenger position updated successfully.', array(), 'Admin.Dsdeliveryhours.Success'));
            } else {
                $msg .= $this->displayError($this->trans('Messenger position updating error', array(), 'Modules.dsfbcustomerchat.Admin'));
            }
        }

        if (!Validate::isString($greetings) == true) {
            $msg .= $this->displayError($this->trans('Greetings must be a text.', array(), 'Modules.dsfbcustomerchat.Admin'));
        } else {
            if (Configuration::updateValue('ds_Fbcustomerchat_GREETINGS', pSQL($greetings))) {
                $msg .= $this->displayConfirmation($this->trans('Greetings updated successfully.', array(), 'Admin.Dsdeliveryhours.Success'));
            } else {
                $msg .= $this->displayError($this->trans('Greetings updating error', array(), 'Modules.dsfbcustomerchat.Admin'));
            }
        }

        if (!Validate::isString($loggedout) == true) {
            $msg .= $this->displayError($this->trans('Logged out message must be a text.', array(), 'Modules.dsfbcustomerchat.Admin'));
        } else {
            if (Configuration::updateValue('ds_Fbcustomerchat_LOGGEDOUT', pSQL($loggedout))) {
                $msg .= $this->displayConfirmation($this->trans('Logged out message updated successfully.', array(), 'Admin.Dsdeliveryhours.Success'));
            } else {
                $msg .= $this->displayError($this->trans('Logged out message updating error', array(), 'Modules.dsfbcustomerchat.Admin'));
            }
        }

        return $msg;
    }

    public function hookDisplayFooter()
    {
        $fb = Configuration::get('ds_Fbcustomerchat_FB');
        $color = Configuration::get('ds_Fbcustomerchat_COLOR');
        $position = Configuration::get('ds_Fbcustomerchat_POSITION');
        $msgLogged = Configuration::get('ds_Fbcustomerchat_GREETINGS');
        $msgOut = Configuration::get('ds_Fbcustomerchat_LOGGEDOUT');
        $locale = $this->context->language->locale;
        $locale = str_replace('-', '_', $locale );


        $this->context->smarty->assign('facebook', array(
            'id' => $fb,
            'color' => $color,
            'position' => $position,
            'msgLogged' => $msgLogged,
            'msgOut' => $msgOut,
            'locale' => $locale
        ));

        $output = $this->display(__FILE__, 'views/templates/hook/hookDisplayFooter.tpl');

        return $output;
    }
}
