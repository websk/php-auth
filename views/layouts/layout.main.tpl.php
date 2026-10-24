<?php
/**
 * @var LayoutDTO $layout_dto
 */

use WebSK\Auth\AuthRoutes;
use WebSK\Auth\Auth;
use WebSK\Slim\Router;
use WebSK\Utils\Assert;
use WebSK\Utils\Messages;
use WebSK\Config\ConfWrapper;
use WebSK\Utils\Sanitize;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Utils\Url;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= Sanitize::sanitizeTagContent($layout_dto->getTitle()) ?></title>

    <!-- jQuery -->
    <script src="/assets/libraries/jquery/jquery.min.js"></script>
    <link href="/assets/libraries/jquery-ui/themes/base/jquery-ui.min.css" rel="stylesheet">
    <script src="/assets/libraries/jquery-ui/jquery-ui.min.js"></script>

    <!-- Bootstrap -->
    <link href="/assets/libraries/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="/assets/libraries/bootstrap/js/bootstrap.min.js"></script>

    <link href="/assets/libraries/font-awesome/css/all.min.css" rel="stylesheet">
    <link href="/assets/libraries/font-awesome/css/v4-shims.min.css" rel="stylesheet">
    <link href="/assets/libraries/crud/crud.css" rel="stylesheet">

    <script src="/assets/libraries/jquery-validation/jquery.validate.min.js"></script>

    <meta name="keywords" content="<?= Sanitize::sanitizeAttrValue($layout_dto->getKeywords()) ?>">
    <meta name="description" content="<?= Sanitize::sanitizeAttrValue($layout_dto->getDescription()) ?>">
    <style>
        body {
            background-color: #fff;
            font-family: Arial, sans-serif;
        }

        #content {
            margin-bottom: 2rem;
        }

        .site-title {
            font-size: 1.375rem;
        }

        #footer {
            background-color: #6c757d;
            color: #fff;
        }
    </style>
</head>
<body>

<div id="html" class="container py-3">
    <header id="header" class="row align-items-center mb-4">
        <div class="site-title col"><?= Sanitize::sanitizeTagContent((string) ConfWrapper::value('site_title')) ?></div>
        <?php if (Auth::getCurrentUserId() !== null) { ?>
            <div class="col-auto text-end"><a href="<?= Sanitize::sanitizeAttrValue(Router::urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGOUT)) ?>">Выйти</a></div>
        <?php } ?>
    </header>

    <main id="content">
                <nav aria-label="Хлебные крошки">
                <ol class="breadcrumb">
                    <?php
                    foreach ($layout_dto->getBreadcrumbsDtoArr() as $breadcrumb_item_dto) {
                        Assert::assert($breadcrumb_item_dto instanceof BreadcrumbItemDTO);
                        if (!$breadcrumb_item_dto->getUrl()) {
                            echo '<li class="breadcrumb-item active" aria-current="page">' . Sanitize::sanitizeTagContent($breadcrumb_item_dto->getName()) . '</li>';
                            continue;
                        }

                        echo '<li class="breadcrumb-item"><a href="' . Sanitize::sanitizeAttrValue(Sanitize::sanitizeUrl($breadcrumb_item_dto->getUrl())) . '">' . Sanitize::sanitizeTagContent($breadcrumb_item_dto->getName()) . '</a></li>';
                    }
                    ?>
                </ol>
                </nav>
                <?php

                $current_url_no_query = Url::getUriNoQueryString();

                if ($current_url_no_query != '/') {
                    ?>
                    <h1><?= Sanitize::sanitizeTagContent($layout_dto->getTitle()) ?></h1>
                    <hr class="d-none d-md-block">
                    <?php
                }
                ?>

                <?php
                echo Messages::renderMessages();
                ?>

                <?php echo $layout_dto->getContentHtml(); ?>
    </main>

    <footer id="footer" class="row rounded p-3">
        <div class="col-12">
            &copy; <?= Sanitize::sanitizeTagContent((string) ConfWrapper::value('site_name')) ?>, <?= date('Y') ?>
        </div>
    </footer>
</div>

</body>
</html>
