<?php
use WebSK\Auth\HybridAuth;

?>

<h3>Вы можете быстро войти на сайт через</h3>
<div class="tm-social-icon-buttons tm-social-icon-buttons-colored">
    <a href="<?php echo HybridAuth::getSocialLoginUrl('Google'); ?>" title="Google" class="uk-icon-button uk-icon-google-plus"></a>
    <a href="<?php echo HybridAuth::getSocialLoginUrl('Vkontakte'); ?>" title="Вконтакте" class="uk-icon-button uk-icon-vk"></a>
    <a href="<?php echo HybridAuth::getSocialLoginUrl('Odnoklassniki'); ?>" title="Одноклассники" class="uk-icon-button uk-icon-odnoklassniki"></a>
    <a href="<?php echo HybridAuth::getSocialLoginUrl('Facebook'); ?>" title="Facebook" class="uk-icon-button uk-icon-facebook"></a>
    <a href="<?php echo HybridAuth::getSocialLoginUrl('Yandex'); ?>" title="Yandex" class="uk-icon-button uk-icon-yc"></a>
    <a href="<?php echo HybridAuth::getSocialLoginUrl('Mailru'); ?>" title="Mail.Ru" class="uk-icon-button uk-icon-at"></a>
    <a href="<?php echo HybridAuth::getSocialLoginUrl('Live'); ?>" title="Windows Live" class="uk-icon-button uk-icon-windows"></a>
</div>
<hr>
<p></p>