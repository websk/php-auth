<?php
use WebSK\Auth\ExternalAuth;

?>

<h3>Вы можете быстро войти на сайт через</h3>
<div class="tm-social-icon-buttons tm-social-icon-buttons-colored">
    <a href="<?php echo ExternalAuth::getExternalLoginUrl('Google'); ?>" title="Google" class="uk-icon-button uk-icon-google-plus"></a>
    <a href="<?php echo ExternalAuth::getExternalLoginUrl('Vkontakte'); ?>" title="Вконтакте" class="uk-icon-button uk-icon-vk"></a>
    <a href="<?php echo ExternalAuth::getExternalLoginUrl('Odnoklassniki'); ?>" title="Одноклассники" class="uk-icon-button uk-icon-odnoklassniki"></a>
    <a href="<?php echo ExternalAuth::getExternalLoginUrl('Yandex'); ?>" title="Yandex" class="uk-icon-button uk-icon-yc"></a>
    <a href="<?php echo ExternalAuth::getExternalLoginUrl('Mailru'); ?>" title="Mail.Ru" class="uk-icon-button uk-icon-at"></a>
</div>
<hr>
<p></p>