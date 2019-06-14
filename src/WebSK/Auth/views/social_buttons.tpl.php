<?php
use WebSK\Auth\Auth;
?>

<h3>Вы можете быстро войти на сайт через</h3>
<div class="tm-social-icon-buttons tm-social-icon-buttons-colored">
    <a href="<?php echo Auth::getSocialLoginUrl('Google'); ?>" title="Google" class="uk-icon-button uk-icon-google-plus"></a>
    <a href="<?php echo Auth::getSocialLoginUrl('Vkontakte'); ?>" title="Вконтакте" class="uk-icon-button uk-icon-vk"></a>
    <a href="<?php echo Auth::getSocialLoginUrl('Odnoklassniki'); ?>" title="Одноклассники" class="uk-icon-button uk-icon-odnoklassniki"></a>
    <a href="<?php echo Auth::getSocialLoginUrl('Facebook'); ?>" title="Facebook" class="uk-icon-button uk-icon-facebook"></a>
    <a href="<?php echo Auth::getSocialLoginUrl('Yandex'); ?>" title="Yandex" class="uk-icon-button uk-icon-yc"></a>
    <a href="<?php echo Auth::getSocialLoginUrl('Mailru'); ?>" title="Mail.Ru" class="uk-icon-button uk-icon-at"></a>
    <a href="<?php echo Auth::getSocialLoginUrl('Live'); ?>" title="Windows Live" class="uk-icon-button uk-icon-windows"></a>
</div>
<hr>
<p></p>