<?php
/**
 * @var $requested_role_id
 * @var User[] $user_objs_arr
 * @var Role[] $role_objs_arr
 */

use WebSK\Auth\Users\Role;
use WebSK\Auth\Users\User;
use WebSK\Image\ImageManager;
use WebSK\Slim\Request;
use WebSK\Slim\Router;
use WebSK\Auth\Users\UsersRoutes;
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-8">
                <form action="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_LIST); ?>" class="form-inline">
                    <div class="form-group">
                        <label>Роль</label>

                        <select name="role_id" class="form-control">
                            <option value="0">Все</option>
                            <?php
                            foreach ($role_objs_arr as $role_obj) {
                                $role_id = $role_obj->getId();
                                echo '<option value="' . $role_id . '" ' . ($role_id == $requested_role_id ? 'selected' : '') . '>' . $role_obj->getName() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Выбрать" class="btn btn-default">
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <a href="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_ADMIN_ROLE_LIST); ?>" class="btn btn-info">
                    <span class="glyphicon glyphicon-wrench"></span> Редактировать роли</a>
            </div>
        </div>
    </div>
</div>

<p class="padding_top_10 padding_bottom_10">
    <a href="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_CREATE); ?>" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить
        пользователя</a>
</p>

<div>
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1 col-sm-1 col-xs-1">
            <col class="col-md-1 hidden-sm hidden-xs">
            <col class="col-md-4 col-sm-6 col-xs-6">
            <col class="col-md-3 hidden-sm hidden-xs">
            <col class="col-md-3 col-sm-5 col-xs-5">
        </colgroup>
        <?php
        foreach ($user_objs_arr as $user_obj) {
            $user_id = $user_obj->getId();
            ?>
            <tr>
                <td><?php echo $user_obj->getId(); ?></td>
                <td class="hidden-xs hidden-sm">
                    <?php
                    if ($user_obj->getPhoto()) {
                        echo '<img src="' . ImageManager::getImgUrlByPreset($user_obj->getPhotoPath(), '30_30') . '" class="img-thumbnail">';
                    }
                    ?>
                </td>
                <td>
                    <a href="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_id]); ?>"><?php echo $user_obj->getName(); ?></a>
                </td>
                <td class="hidden-xs hidden-sm"><?php echo $user_obj->getEmail(); ?></td>
                <td align="right">
                    <a href="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_id]); ?>" title="Редактировать"
                       class="btn btn-default btn-sm">
                        <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
                    </a>
                    <a href="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_USER_DELETE, ['user_id' => $user_id], ['destination' => Request::getUri()->getPath()]); ?>"
                       onClick="return confirm('Вы уверены, что хотите удалить?')" title="Удалить"
                       class="btn btn-default btn-sm">
                        <span class="fa fa-trash-o fa-lg text-danger fa-fw"></span>
                    </a>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
