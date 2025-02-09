# WebSK php-auth

## Install

https://packagist.org/packages/websk/php-auth

install dependency using Composer

```shell
composer require websk/php-auth
```

## Config example
* config/config.default.php

## Demo
* copy config/config.default.php as config/config.php
* set and replace settings and paths
* composer update
* create MySQL DB db_auth (or other) 
* run auto process migration in MySQL DB: `php vendor\bin\websk_db_migration.php migrations:migration_auto`
  or run handle process migration in MySQL DB `php vendor\bin\websk_db_migration.php migrations:migration_handle`
* run process create user: `php bin\websk_auth_create_user.php auth:create_user`
* cd public
* php -S localhost:8000
* open http://localhost:8000
* login as created user