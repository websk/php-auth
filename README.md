# WebSK php-auth

## Config example
* config/config.default.php

## Demo
* copy config/config.default.php as config/config.php
* replace settings and paths
* composer update
* create MySQL DB db_auth (or other) 
* process migration in MySQL DB: `php vendor\bin\websk_db_migration.php migrations:migration_auto` or `php vendor\bin\websk_db_migration.php migrations:migration_handle`
* load in MySQL DB db_auth src/WebSK/Auth/Demo/demo_dump.sql
* cd public
* php -S localhost:8000
* open http://localhost:8000
* login as demo@websk.ru with password 12345