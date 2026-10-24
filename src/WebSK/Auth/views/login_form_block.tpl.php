<?php
/**
 * @var string $destination
 */

use WebSK\Auth\AuthRoutes;
use WebSK\Slim\Router;

?>

<form action="<?= Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN) ?>" class="d-flex flex-wrap align-items-center gap-2" method="post">
    <div>
        <label for="login_block_email" class="visually-hidden">Email</label>
        <div class="input-group">
            <span class="input-group-text">@</span>
            <input id="login_block_email" type="email" name="email" placeholder="Email" class="form-control" autocomplete="email" required>
        </div>
    </div>
    <div>
        <label for="login_block_password" class="visually-hidden">Пароль</label>
        <input id="login_block_password" type="password" name="password" placeholder="Пароль" class="form-control" autocomplete="current-password" required>
    </div>
    <div class="form-check">
        <input id="login_block_save_auth" class="form-check-input" type="checkbox" name="save_auth" value="1">
        <label class="form-check-label" for="login_block_save_auth">Запомнить</label>
    </div>
    <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
    <button type="submit" class="btn btn-secondary">Войти</button>
</form>
