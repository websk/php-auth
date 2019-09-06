<?php
/**
 * @var User $user_obj
 * @var string $redirect_destination_url
 */

use WebSK\Image\ImageManager;
use WebSK\Slim\Router;
use WebSK\Auth\User\User;
use WebSK\Auth\User\UserRoutes;
use WebSK\Utils\Url;

$destination = $redirect_destination_url ?: Url::getUriNoQueryString();

?>
<form id="profile_form" action="<?php echo Router::pathFor(UserRoutes::ROUTE_NAME_USER_ADD_PHOTO, ['user_id' => $user_obj->getId()]); ?>" autocomplete="off" method="post" class="form-horizontal" enctype="multipart/form-data">

    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <h3>Фотография пользователя</h3>
            <?php
            if (!$user_obj->getPhoto()) {
                ?>
                <div class="form-group">
                    <input type="file" name="image_file" size="12">
                </div>
            <?php
            } else {
            ?>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $("a#user_photo").fancybox({});
                    });
                </script>
                <a id="user_photo"
                   href="<?php echo ImageManager::getImgUrlByPreset($user_obj->getPhotoPath(), '600_auto'); ?>">
                    <img
                        src="<?php echo ImageManager::getImgUrlByPreset($user_obj->getPhotoPath(), '200_auto'); ?>"
                        border="0" class="img-responsive img-thumbnail">
                </a>

                <div>
                    <a href="<?php echo Router::pathFor(UserRoutes::ROUTE_NAME_USER_DELETE_PHOTO, ['user_id' => $user_obj->getId()], ['destination' => $destination]); ?>"
                       class="btn btn-default">Удалить фото</a>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <input type="hidden" name="destination" value="<?php echo $destination; ?>">
            <input type="submit" value="Загрузить фото" class="btn btn-primary">
        </div>
    </div>
</form>
