# WebSK php-auth

Authentication and user-management components for the WebSK/Slim stack.

## Requirements

* PHP 8.3 or newer; the demo Docker environment uses PHP 8.5.11.
* PDO with the MySQL driver.
* The extensions required by the selected WebSK cache, CAPTCHA and file-storage backends.

The current dependency stack uses Slim 4.15, PHP-DI 7 with PHP attributes and the WebSK 3.x database/entity/CRUD packages.

## Install

https://packagist.org/packages/websk/php-auth

install dependency using Composer

```shell
composer require websk/php-auth
```

## Config
* php-auth no special configs

Existing installations must apply the SQL statements in `dumps/db_auth.sql`. The password column is widened to 255 characters and the confirmation-code column to 64 characters.

New passwords use `password_hash()`. Legacy salted MD5 passwords remain valid for one successful login and are then automatically replaced with a modern hash. Session and confirmation tokens are generated with `random_bytes()`, and authentication cookies use `HttpOnly`, `SameSite=Lax` and `Secure` on HTTPS requests.

## Development checks

```shell
composer validate --strict
composer check-platform-reqs
composer dump-autoload --optimize --strict-psr
composer check
composer audit --locked
```

MySQL integration tests can be run separately with:

```shell
composer test:mysql
```

## Demo

* Установить mkcert, https://github.com/FiloSottile/mkcert

* Выполнить:
  ```shell
  mkcert --install
  ```

* Сделать самоподписанный сертификат для `php-auth.devbox`:

  ```shell
  mkcert php-auth.devbox
  ```

* Скопировать полученные файлы _wildcard.php-auth.devbox.pem и _wildcard.php-auth.devbox.pem в `var/docker/nginx/sites`

* Прописать в `/etc/hosts` или аналог в Windows `%WINDIR%\System32\drivers\etc\hosts`

    ```
    127.0.0.1 php-auth.devbox
    ```

* Создаем локальный конфиг, при необходимости вносим изменения:

  ```shell
  cp config/config.default.php config/config.php
  ```

* Заходим в директорию с docker-compose:

  ```shell
  cd var/docker
  ```

* Создаем локальный env файл, при необходимости вносим изменения:

  ```shell
  cp .example.env .env
  ```

* Собираем и запускаем докер-контейнеры:

  ```shell
  docker compose up -d --build
  ```

* Проверяем, что контейнер использует PHP 8.5.11, и устанавливаем зависимости:

  ```shell
  docker compose exec php-fpm php -v
  docker compose exec php-fpm composer install
  ```

* Выполняем миграции БД

  ```shell
  docker compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_auto
  ```

  or run handle process migration:

  ```shell
  docker compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_handle
  ```

* Создаем пользователя для входа в админку

  ```shell
  docker compose exec php-fpm php bin/websk_auth_create_user.php auth:create_user
  ```

* open `https://php-auth.devbox`
