<?php
/**
 * @var User $user_obj
 * @var string $save_handler_url
 */

use WebSK\Slim\Router;
use WebSK\Auth\Auth;
use WebSK\Auth\Users\User;
use WebSK\Auth\Users\UsersRoutes;
use WebSK\Utils\Url;

$destination = Url::getUriNoQueryString();

?>
<form id="profile_form" action="<?php echo $save_handler_url; ?>" autocomplete="off" method="post" class="form-horizontal">

    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <h3>Смена пароля</h3>
        </div>
    </div>

    <div>
        <div class="form-group">
            <label class="col-md-4 control-label">Пароль</label>
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
                    <a href="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_USER_CREATE_PASSWORD, ['user_id' => $user_obj->getId()], ['destination' => $destination]) ?>">Сгенерировать
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
            <input type="submit" value="Сохранить изменения" class="btn btn-primary">
        </div>
    </div>
</form>

