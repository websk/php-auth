<?php

use WebSK\Auth\AuthRoutes;
use WebSK\Captcha\CaptchaRoutes;
use WebSK\Slim\Router;
use WebSK\Auth\User\User;

$destination = Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM);

$user_obj = new User();
?>
<form id="registration_form" action="<?php echo Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_REGISTRATION); ?>" autocomplete="off" method="post" class="form-horizontal">
    <div xmlns="http://www.w3.org/1999/html">
        <div class="form-group">
            <label class="col-md-4 control-label">Имя на сайте</label>
            <div class="col-md-8">
                <input type="text" name="name" value="" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">E-mail</label>
            <div class="col-md-8">
                <input type="text" name="email" value="" class="form-control">
            </div>
        </div>
        <div>
            <div class="form-group">
                <label class="col-md-4 control-label">Пароль</label>
                <div class="col-md-8">
                    <input type="password" name="new_password_first" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">Подтверждение пароля</label>
                <div class="col-md-8">
                    <input type="password" name="new_password_second" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <img src="<?php echo Router::urlFor(CaptchaRoutes::ROUTE_NAME_CAPTCHA_GENERATE); ?>" border="0" alt="Введите этот защитный код">
            <input type="text" size="5" name="captcha" class="form-control">
            <span class="help-block">Введите код, изображенный на картинке</span>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <input type="hidden" name="destination" value="<?php echo $destination; ?>">
            <input type="submit" value="Зарегистрироваться" class="btn btn-primary">
        </div>
    </div>
</form>

<script type="text/javascript">
    $().ready(function () {
        $("#registration_form").validate({
            ignore: ":hidden",
            rules: {
                name: "required",
                email: "required",
                new_password_first: "required",
                new_password_second: "required"
            },
            messages: {
                name: "Это поле обязательно для заполнения",
                email: "Это поле обязательно для заполнения",
                new_password_first: "Это поле обязательно для заполнения",
                new_password_second: "Это поле обязательно для заполнения"
            }
        });
    })
</script>
