# WebSK PHP Auth

Authentication, session, user, and role management for applications built with
Slim and the WebSK component stack.

The package provides:

- login, logout, registration, email confirmation, and password recovery;
- secure password hashing with automatic migration of legacy salted MD5 hashes;
- database-backed authentication sessions and persistent-login cookies;
- role-based authorization helpers and administrator middleware;
- user and role administration through WebSK CRUD;
- user profile editing and photo storage;
- a CLI command for creating an administrator;
- a Docker-based demo application.

## Requirements

- PHP 8.3 or newer;
- Composer 2;
- PDO and the `pdo_mysql` extension;
- a MySQL-compatible database;
- the extensions required by the configured cache, CAPTCHA, and file-storage
  backends.

The included demo additionally requires Docker Compose, Node.js 20, npm 10,
and [mkcert](https://github.com/FiloSottile/mkcert). Its PHP-FPM image is pinned
to PHP 8.5.11.

The current stack uses Slim 4.15, PHP-DI 7 with attributes, WebSK DB and Entity
3.x, WebSK CRUD 4.0.x, and WebSK View 3.1.x.

## Installation

Install the package with Composer:

```shell
composer require websk/php-auth
```

Package information is available on
[Packagist](https://packagist.org/packages/websk/php-auth).

## Application integration

The application container must support PSR-11 access and array-style service
registration. Register the providers in this order:

```php
use WebSK\Auth\AuthServiceProvider;
use WebSK\Auth\User\UserServiceProvider;
use WebSK\Cache\CacheServiceProvider;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\MutableContainerAdapter;

$mutableContainer = new MutableContainerAdapter($container);

CacheServiceProvider::register($mutableContainer);
CRUDServiceProvider::register($mutableContainer);
AuthServiceProvider::register($mutableContainer);
UserServiceProvider::register($mutableContainer);
```

Initialize `ConfWrapper` with the application's `settings` array. With PHP-DI,
expose Slim's route parser through the container and then give the application
instance to the WebSK facades:

```php
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Interfaces\RouteParserInterface;
use WebSK\Config\ConfWrapper;
use WebSK\Slim\Facade;

ConfWrapper::setConfig($config['settings']);

$routeCollector = $app->getRouteCollector();
$routeCollector->setDefaultInvocationStrategy(
    $container->get(InvocationStrategyInterface::class)
);
$container->set(RouteParserInterface::class, $routeCollector->getRouteParser());

Facade::setFacadeApplication($app);
```

Register the public authentication, CAPTCHA, profile, and administrator routes:

```php
use Slim\Interfaces\RouteCollectorProxyInterface;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\User\UserRoutes;
use WebSK\Captcha\CaptchaRoutes;

CaptchaRoutes::register($app);
AuthRoutes::register($app);
UserRoutes::register($app);

$app->group('/admin', function (RouteCollectorProxyInterface $group): void {
    UserRoutes::registerAdmin($group);
});
```

`UserRoutes::registerAdmin()` applies the administrator middleware to its own
route group. See
[`AuthDemoApp.php`](src/WebSK/Auth/Demo/AuthDemoApp.php) and
[`public/index.php`](public/index.php) for a complete PHP-DI and Slim setup.

## Configuration

[`config/config.default.php`](config/config.default.php) documents the complete
settings structure. For the standalone demo, copy this file to the local
`config.php` before starting the application:

```shell
cp config/config.default.php config/config.php
```

Edit `config/config.php` and replace the default database credentials, site
URL, email address, filesystem paths, and other environment-specific values.
The local file is excluded from Git, so deployment credentials and machine
paths do not need to be committed. Keep `config/config.default.php` as the
version-controlled example containing safe defaults and the complete settings
structure.

When installing the package into another application, add the same settings to
that application's configuration instead. Review at least the following
values:

| Setting | Purpose |
| --- | --- |
| `settings.db.db_auth.host` | Authentication database host |
| `settings.db.db_auth.db_name` | Authentication database name |
| `settings.db.db_auth.user` | Database user |
| `settings.db.db_auth.password` | Database password |
| `settings.db.db_auth.dump_file_path` | Path to `dumps/db_auth.sql` for WebSK migrations |
| `settings.cache` | Cache engine, key prefix, and backend servers |
| `settings.auth.layout_main` | Layout used by public authentication pages |
| `settings.auth.layout_admin` | Layout used by administrator pages |
| `settings.auth.main_page_url` | Redirect target for ordinary users |
| `settings.auth.admin_main_page_url` | Redirect target for administrators |
| `settings.auth.default_user_role_id` | Optional role assigned during registration |
| `settings.auth.salt` | Stable salt used only to verify legacy MD5 passwords |
| `settings.site_domain` | Absolute site origin used in confirmation emails |
| `settings.site_name` | Site name used in pages and emails |
| `settings.site_email` | Sender address used by PHPMailer |
| `settings.site_full_path` | Absolute application path |
| `settings.log_path` | Writable application log directory |
| `settings.tmp_path` | Writable temporary directory |
| `settings.files_data_path` | Local path for uploaded files |
| `settings.storages.files` | File-manager adapter, paths, and allowed file types |

The application must also configure the cache service. The demo uses Memcached.
Mail delivery uses PHPMailer and the PHP mail transport unless the host
application configures another transport.

## Database schema and upgrades

The schema in [`dumps/db_auth.sql`](dumps/db_auth.sql) creates the `users`,
`roles`, `users_roles`, and `sessions` tables and inserts the administrator role
with ID `1` and designation `ADMINS`.

Apply the schema and all pending statements with the WebSK migration command:

```shell
php vendor/bin/websk_db_migration.php migrations:migration_auto
```

To review and apply statements interactively, run:

```shell
php vendor/bin/websk_db_migration.php migrations:migration_handle
```

Existing installations must apply the entire current migration stream. In
particular, `users.passw` must be `VARCHAR(255)`, `users.confirm_code` must be
`VARCHAR(64)`, and `sessions.session` must be `VARCHAR(64)`.

## Authentication and authorization

The `WebSK\Auth\Auth` facade exposes the most commonly used checks:

```php
use WebSK\Auth\Auth;

$userId = Auth::getCurrentUserId();
$user = Auth::getCurrentUserObj();
$isAdmin = Auth::currentUserIsAdmin();
$canPublish = Auth::currentUserHasAccessByRoleDesignation('PUBLISHERS');
$hasAnyRole = Auth::currentUserHasAccessByAnyRoleDesignations([
    'EDITORS',
    'PUBLISHERS',
]);
```

New passwords use `password_hash()` and `password_verify()`. A legacy salted
MD5 password remains valid for one successful login and is then replaced with a
modern password hash. Session and confirmation tokens are generated with
`random_bytes()`. Authentication cookies use `HttpOnly`, `SameSite=Lax`, and
the `Secure` flag on HTTPS requests.

## Routes

`AuthRoutes::register()` adds these public routes:

| Method | Path | Purpose |
| --- | --- | --- |
| GET | `/auth/login_form` | Login form |
| POST | `/auth/login` | Login submission |
| GET | `/auth/logout` | Logout |
| GET | `/auth/registration_form` | Registration form |
| POST | `/auth/registration` | Registration submission |
| GET | `/auth/confirm_registration/{confirm_code}` | Email confirmation |
| GET | `/auth/send_confirm_code_form` | Confirmation-code request form |
| POST | `/auth/send_confirm_code` | Send a new confirmation email |
| GET | `/auth/forgot_password_form` | Password-recovery form |
| POST | `/auth/forgot_password` | Password recovery |

`UserRoutes::register()` adds profile editing and password-management routes
under `/user`. `UserRoutes::registerAdmin()` adds user and role CRUD routes
under `/user` relative to the group supplied by the application; the demo
mounts that group at `/admin`.

## Administrator CLI

Create a confirmed administrator with an explicit email address and password:

```shell
php vendor/bin/websk_auth_create_user.php auth:create_user \
  --email=admin@example.test \
  --password='replace-with-a-strong-password'
```

When this repository is used directly, `bin/websk_auth_create_user.php` can be
used instead of the Composer-installed binary. If the options are omitted, the
command generates demo credentials and prints them to the terminal.

## Demo with Docker

1. Add the local hostname to `/etc/hosts` or the Windows hosts file:

   ```text
   127.0.0.1 php-auth.devbox
   ```

2. Install the local mkcert certificate authority:

   ```shell
   mkcert -install
   ```

3. Generate the certificate files expected by nginx:

   ```shell
   cd var/docker/nginx/sites
   mkcert -cert-file php-auth.devbox.pem \
     -key-file php-auth.devbox-key.pem \
     php-auth.devbox
   cd ../../../..
   ```

4. Create the local application and Docker configuration files:

   ```shell
   cp config/config.default.php config/config.php
   cp var/docker/.example.env var/docker/.env
   ```

   Set `settings.site_domain` in `config/config.php` to
   `https://php-auth.devbox`. Adjust the database, mail, storage, and Docker
   settings when necessary.

5. Build and start the services:

   ```shell
   docker compose --env-file var/docker/.env \
     -f var/docker/docker-compose.yml up -d --build
   ```

6. Install PHP dependencies inside the PHP-FPM container:

   ```shell
   docker compose --env-file var/docker/.env \
     -f var/docker/docker-compose.yml exec php-fpm composer install
   ```

7. Build the local frontend assets. Composer installation must run first
   because the asset builder copies the WebSK CRUD stylesheet from `vendor`:

   ```shell
   npm ci
   npm run build
   npm run check:assets
   ```

8. Apply the database schema:

   ```shell
   docker compose --env-file var/docker/.env \
     -f var/docker/docker-compose.yml exec php-fpm \
     php vendor/bin/websk_db_migration.php migrations:migration_auto
   ```

9. Create an administrator:

   ```shell
   docker compose --env-file var/docker/.env \
     -f var/docker/docker-compose.yml exec php-fpm \
     php bin/websk_auth_create_user.php auth:create_user \
     --email=admin@example.test \
     --password='replace-with-a-strong-password'
   ```

10. Open <https://php-auth.devbox>.

The Compose stack starts nginx, PHP-FPM, MySQL 8.0, and Memcached. Stop it with:

```shell
docker compose --env-file var/docker/.env \
  -f var/docker/docker-compose.yml down
```

## Frontend assets

The repository keeps generated assets out of Git. The npm build copies pinned
Bootstrap 5, jQuery, jQuery UI, jQuery Validation, Font Awesome, Blueimp File
Upload, and WebSK CRUD assets into `public/assets/libraries`:

```shell
npm ci
npm run build
npm run check:assets
```

Host applications may use their own asset pipeline, but the templates and CRUD
widgets require compatible copies of these libraries.

The demo loads the canonical Bootstrap bundle from
`/assets/libraries/bootstrap/js/bootstrap.bundle.min.js`. The build retains
`bootstrap.min.js` as a compatibility alias and publishes the referenced
Bootstrap JavaScript and CSS source maps.

## Development and verification

Install PHP and frontend dependencies before running the complete checks:

```shell
composer install
npm ci
npm run build

composer validate --strict
composer check-platform-reqs
composer dump-autoload --optimize --strict-psr
composer check
composer audit --locked
npm run check:assets
npm audit --omit=dev
git diff --check
```

`composer check` runs PHP syntax validation, PHPUnit, and PHPStan at the maximum
level.

The MySQL integration test creates and removes an isolated temporary database.
With the demo stack running, execute it inside PHP-FPM:

```shell
docker compose --env-file var/docker/.env \
  -f var/docker/docker-compose.yml exec \
  -e MYSQL_TEST_HOST=mysql \
  -e MYSQL_TEST_USER=root \
  -e MYSQL_TEST_PASSWORD=root \
  php-fpm composer test:mysql
```

## License

This project is released under the [MIT License](LICENSE).
