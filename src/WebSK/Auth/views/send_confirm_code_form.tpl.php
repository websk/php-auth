<?php

use WebSK\Auth\AuthRoutes;
use WebSK\Captcha\CaptchaRoutes;
use WebSK\Slim\Router;

?>

<form action="<?= Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_SEND_CONFIRM_CODE) ?>" method="post">
    <div class="row mb-3">
        <label for="confirm_code_email" class="col-md-2 col-form-label">Email</label>
        <div class="col-md-10">
            <input id="confirm_code_email" type="email" name="email" class="form-control" aria-describedby="confirm_code_email_help" autocomplete="email" required>
            <div id="confirm_code_email_help" class="form-text">Введите адрес электронной почты, который вы указывали при регистрации</div>
        </div>
    </div>

    <div class="row mb-3">
        <label for="confirm_code_captcha" class="col-md-2 col-form-label">Защитный код</label>
        <div class="col-md-10">
            <img src="<?= Router::urlFor(CaptchaRoutes::ROUTE_NAME_CAPTCHA_GENERATE) ?>" class="d-block mb-2" alt="Изображение защитного кода">
            <input id="confirm_code_captcha" type="text" name="captcha" class="form-control" aria-describedby="confirm_code_captcha_help" autocomplete="off" required>
            <div id="confirm_code_captcha_help" class="form-text">Введите код, изображённый на картинке</div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="offset-md-2 col-md-10">
            <button type="submit" class="btn btn-primary">Получить ссылку</button>
        </div>
    </div>

    <div class="row mb-3">
        <div class="offset-md-2 col-md-10">
            <a href="<?php echo Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_REGISTRATION_FORM); ?>">Регистрация</a>
        </div>
    </div>
</form>
