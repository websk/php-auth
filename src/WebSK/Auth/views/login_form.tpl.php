<?php

use WebSK\Auth\AuthRoutes;
use WebSK\Slim\Router;

$destination = '/';
if (isset($_REQUEST['destination']) && is_string($_REQUEST['destination'])) {
    $destination = $_REQUEST['destination'];
}
?>

<form action="<?= Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN) ?>" method="post">
    <div class="row mb-3">
        <label for="auth_email" class="col-md-2 col-form-label">Email</label>
        <div class="col-md-10">
            <input id="auth_email" type="email" name="email" class="form-control" autocomplete="email" required>
        </div>
    </div>
    <div class="row mb-3">
        <label for="auth_password" class="col-md-2 col-form-label">Пароль</label>
        <div class="col-md-10">
            <input id="auth_password" type="password" name="password" class="form-control" autocomplete="current-password" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="offset-md-2 col-md-10">
            <div class="form-check">
                <input id="auth_save_auth" class="form-check-input" type="checkbox" name="save_auth" value="1">
                <label class="form-check-label" for="auth_save_auth">Запомнить меня</label>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="offset-md-2 col-md-10">
            <button type="submit" class="btn btn-primary">Войти</button>
        </div>
    </div>
    <div class="row mb-3">
        <div class="offset-md-2 col-md-10">
            <a href="<?php echo Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_FORGOT_PASSWORD_FORM); ?>">Забыли пароль</a> /
            <a href="<?php echo Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_REGISTRATION_FORM); ?>">Регистрация</a>
        </div>
    </div>
    <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
</form>
