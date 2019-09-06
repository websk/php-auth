<?php
/**
 * @var User $user_obj
  * @var string $redirect_destination_url
 */

use WebSK\Auth\Auth;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserRoutes;
use WebSK\Slim\Router;
use WebSK\Utils\Url;

$destination = $redirect_destination_url ?: Url::getUriNoQueryString();

?>
<form id="profile_form" action="<?php echo Router::pathFor(UserRoutes::ROUTE_NAME_USER_UPDATE, ['user_id' => $user_obj->getId()]); ?>" autocomplete="off" method="post"
      class="form-horizontal" enctype="multipart/form-data">
    <div xmlns="http://www.w3.org/1999/html">
        <div class="form-group has-warning">
            <label class="col-md-4 control-label">Имя на сайте</label>

            <div class="col-md-8">
                <input type="text" name="name" value="<?= $user_obj->getName() ?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">Имя</label>

            <div class="col-md-8">
                <input type="text" name="first_name" value="<?= $user_obj->getFirstName() ?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">Фамилия</label>

            <div class="col-md-8">
                <input type="text" name="last_name" value="<?= $user_obj->getLastName() ?>" class="form-control">
            </div>
        </div>
        <div class="form-group has-warning">
            <label class="col-md-4 control-label">E-mail</label>

            <div class="col-md-8">
                <input type="text" name="email" value="<?= $user_obj->getEmail() ?>" class="form-control">
            </div>
        </div>
        <?php
        if (Auth::currentUserIsAdmin()) {
            ?>
            <div class="form-group">
                <div class="col-md-offset-4 col-md-8">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="confirm"
                                   value="1"<?= $user_obj->isConfirm() ? ' checked' : '' ?>> Регистрация подтверждена
                        </label>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="form-group">
            <label class="col-md-4 control-label">Дата рождения</label>

            <div class="col-md-8">
                <input type="text" name="birthday" value="<?= $user_obj->getBirthDay() ?>" maxlength="10"
                       class="form-control">
                <span class="help-block">(дд.мм.гггг)</span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">Телефон</label>

            <div class="col-md-8">
                <input type="text" name="phone" value="<?= $user_obj->getPhone() ?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">Город</label>

            <div class="col-md-8">
                <input type="text" name="city" value="<?= $user_obj->getCity() ?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">Адрес</label>

            <div class="col-md-8">
                <input type="text" name="address" value="<?= $user_obj->getAddress() ?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">Дополнительная информация</label>

            <div class="col-md-8">
                <textarea name="comment" rows="7" class="form-control"><?= $user_obj->getComment() ?></textarea>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <input type="hidden" name="destination" value="<?php echo $destination; ?>">
            <input type="submit" value="Сохранить изменения" class="btn btn-primary">
        </div>
    </div>
</form>

<script type="text/javascript">
    $().ready(function () {
        $.validator.setDefaults({
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });

        $("#profile_form").validate({
            ignore: ":hidden",
            rules: {
                name: "required",
                email: "required"
            },
            messages: {
                name: "Это поле обязательно для заполнения",
                email: "Это поле обязательно для заполнения"
            }
        });
    })
</script>
