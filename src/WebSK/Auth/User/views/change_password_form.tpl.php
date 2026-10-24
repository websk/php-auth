<?php
/**
 * @var User $user_obj
 * @var string $redirect_destination_url
 */

use WebSK\Auth\User\User;
use WebSK\Slim\Router;
use WebSK\Auth\Auth;
use WebSK\Auth\User\UserRoutes;
use WebSK\Utils\Url;

$destination = $redirect_destination_url ?: Url::getUriNoQueryString();

?>
<form id="profile_form" action="<?= Router::urlFor(UserRoutes::ROUTE_NAME_USER_CHANGE_PASSWORD, ['user_id' => (string) $user_obj->getId()]) ?>" autocomplete="off" method="post">

    <div class="row mb-3">
        <div class="offset-md-4 col-md-8">
            <h3>Смена пароля</h3>
        </div>
    </div>

    <div>
        <div class="row mb-3">
            <label for="profile_password" class="col-md-4 col-form-label">Новый пароль</label>
            <div class="col-md-8">
                <input id="profile_password" type="password" name="new_password_first" class="form-control" autocomplete="new-password" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="profile_password_confirmation" class="col-md-4 col-form-label">Подтверждение пароля</label>
            <div class="col-md-8">
                <input id="profile_password_confirmation" type="password" name="new_password_second" class="form-control" autocomplete="new-password" required>
            </div>
        </div>

        <?php
        if (Auth::currentUserIsAdmin()) {
            ?>
            <div class="row mb-3">
                <div class="offset-md-4 col-md-8">
                    <a href="<?php echo Router::urlFor(UserRoutes::ROUTE_NAME_USER_CREATE_PASSWORD, ['user_id' => (string) $user_obj->getId()], ['destination' => $destination]) ?>">Сгенерировать
                        пароль и выслать пользователю</a>
                </div>
            </div>
            <?php
        }
        ?>
    </div>

    <div class="row mb-3">
        <div class="offset-md-4 col-md-8">
            <input type="hidden" name="destination" value="<?= htmlspecialchars($destination, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            <button type="submit" class="btn btn-primary">Изменить пароль</button>
        </div>
    </div>
</form>
