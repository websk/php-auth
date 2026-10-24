<?php

use WebSK\Auth\AuthRoutes;
use WebSK\Captcha\CaptchaRoutes;
use WebSK\Slim\Router;
use WebSK\Auth\User\User;

$destination = Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM);

$user_obj = new User();
?>
<form id="registration_form" action="<?= Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_REGISTRATION) ?>" autocomplete="off" method="post">
    <div class="row mb-3">
        <label for="registration_name" class="col-md-4 col-form-label">Имя на сайте</label>
        <div class="col-md-8">
            <input id="registration_name" type="text" name="name" value="" class="form-control" autocomplete="name" required>
        </div>
    </div>
    <div class="row mb-3">
        <label for="registration_email" class="col-md-4 col-form-label">E-mail</label>
        <div class="col-md-8">
            <input id="registration_email" type="email" name="email" value="" class="form-control" autocomplete="email" required>
        </div>
    </div>
    <div class="row mb-3">
        <label for="registration_password" class="col-md-4 col-form-label">Пароль</label>
        <div class="col-md-8">
            <input id="registration_password" type="password" name="new_password_first" class="form-control" autocomplete="new-password" required>
        </div>
    </div>
    <div class="row mb-3">
        <label for="registration_password_confirmation" class="col-md-4 col-form-label">Подтверждение пароля</label>
        <div class="col-md-8">
            <input id="registration_password_confirmation" type="password" name="new_password_second" class="form-control" autocomplete="new-password" required>
        </div>
    </div>
    <div class="row mb-3">
        <label for="registration_captcha" class="col-md-4 col-form-label">Защитный код</label>
        <div class="col-md-8">
            <img src="<?= Router::urlFor(CaptchaRoutes::ROUTE_NAME_CAPTCHA_GENERATE) ?>" class="d-block mb-2" alt="Изображение защитного кода">
            <input id="registration_captcha" type="text" name="captcha" class="form-control" aria-describedby="registration_captcha_help" autocomplete="off" required>
            <div id="registration_captcha_help" class="form-text">Введите код, изображённый на картинке</div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="offset-md-4 col-md-8">
            <input type="hidden" name="destination" value="<?= htmlspecialchars($destination, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
        </div>
    </div>
</form>

<script type="text/javascript">
    $().ready(function () {
        $("#registration_form").validate({
            ignore: ":hidden",
            errorElement: "div",
            errorClass: "invalid-feedback d-block",
            highlight: function (element) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid");
            },
            rules: {
                name: "required",
                email: "required",
                new_password_first: "required",
                new_password_second: {
                    required: true,
                    equalTo: "#registration_password"
                },
                captcha: "required"
            },
            messages: {
                name: "Это поле обязательно для заполнения",
                email: "Это поле обязательно для заполнения",
                new_password_first: "Это поле обязательно для заполнения",
                new_password_second: {
                    required: "Это поле обязательно для заполнения",
                    equalTo: "Пароли должны совпадать"
                },
                captcha: "Это поле обязательно для заполнения"
            }
        });
    });
</script>
