# WebSK php-auth

## Config example
* config/config.default.php

## Demo
* copy config/config.default.php as config/config.php
* set and replace settings and paths
* composer update
* create MySQL DB db_auth (or other) 
* process migration in MySQL DB: `php vendor\bin\websk_db_migration.php migrations:migration_auto` or `php vendor\bin\websk_db_migration.php migrations:migration_handle`
* process create user: `php bin\websk_auth_create_user.php auth:create_user`
* cd public
* php -S localhost:8000
* open http://localhost:8000
* login as created user