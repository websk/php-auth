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
<form id="profile_form" action="<?php echo Router::urlFor(UserRoutes::ROUTE_NAME_USER_CHANGE_PASSWORD, ['user_id' => $user_obj->getId()]); ?>" autocomplete="off" method="post" class="form-horizontal">

    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <h3>Смена пароля</h3>
        </div>
    </div>

    <div>
        <div class="form-group">
            <label class="col-md-4 control-label">Новый пароль</label>
            <div class="col-md-8">
                <input type="password" name="new_password_first" class="form-control" autocomplete="new-password">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">Подтверждение пароля</label>
            <div class="col-md-8">
                <input type="password" name="new_password_second" class="form-control">
            </div>
        </div>

        <?php
        if (Auth::currentUserIsAdmin()) {
            ?>
            <div class="form-group">
                <div class="col-md-offset-4 col-md-8">
                    <a href="<?php echo Router::urlFor(UserRoutes::ROUTE_NAME_USER_CREATE_PASSWORD, ['user_id' => $user_obj->getId()], ['destination' => $destination]) ?>">Сгенерировать
                        пароль и выслать пользователю</a>
                </div>
            </div>
            <?php
        }
        ?>
    </div>

    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <input type="hidden" name="destination" value="<?php echo $destination; ?>">
            <input type="submit" value="Изменить пароль" class="btn btn-primary">
        </div>
    </div>
</form>

