{*
*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <DARK SIDE TEAM> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Poul-Henning Kamp
 * ----------------------------------------------------------------------------
 *
*}
<!-- Messenger Wtyczka czatu Code -->
<div id="fb-root"></div>

<!-- Your Wtyczka czatu code -->
<div id="fb-customer-chat" class="fb-customerchat" page_id="{$facebook.id}" theme_color="{$facebook.color}" logged_out_greeting="{$facebook.msgOut}" logged_in_greeting="{$facebook.msgLogged}">
</div>

<script>
  var chatbox = document.getElementById('fb-customer-chat');
  chatbox.setAttribute("attribution", "biz_inbox");
  var locale = "{$facebook.locale}";
</script>

<!-- Your SDK code -->
<script>
  window.fbAsyncInit = function() {
FB.init({
  xfbml: true,
  version  : 'v13.0'
});
  };

  (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/{$facebook.locale}/sdk/xfbml.customerchat.js';
  fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>