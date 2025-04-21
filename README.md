# WebSK php-auth

## Install

https://packagist.org/packages/websk/php-auth

install dependency using Composer

```shell
composer require websk/php-auth
```

## Config
* php-auth no special configs

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
  cp config/config.example.php config/config.php
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

* Устанавливаем зависимости для проекта

  ```shell
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
