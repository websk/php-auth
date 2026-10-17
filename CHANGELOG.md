# Changelog

All notable changes to this project are documented in this file.

The historical entries were reconstructed from the tags, commit ranges, and
GitHub Releases in the remote `websk/php-auth` repository. Release descriptions
were empty except for `2.0.4`, so the notes below reflect the actual tagged
diffs and commit history.

## Unreleased

## 3.0.0 - 2026-10-17

### Added

- Added PHPUnit 12.5 tests for password hashing, legacy-password migration,
  secure session tokens, and the MySQL schema.
- Added PHPStan 2.3 configuration at level `max` and Composer scripts for lint,
  tests, static analysis, and the complete check suite.
- Added the MIT license.

### Changed

- Updated the development and Docker runtime to PHP 8.5.11.
- Updated Slim to 4.15, PHP-DI to 7.1, PHPMailer to 7.1, and the compatible
  WebSK dependency set, including the 3.x cache, CRUD, database, entity,
  file-manager, and utility packages.
- Replaced PHP-DI annotation injection with PHP attributes and adapted service
  registration to mutable PSR-11 containers.
- Replaced decorated Slim request accessors with PSR-7 request parsing.
- Corrected PSR-4 and development autoload mappings.
- New passwords now use `password_hash()` and `password_verify()`. A successful
  login with a legacy salted MD5 password transparently upgrades the stored
  hash.
- Session and confirmation tokens now use cryptographically secure random
  bytes. Authentication cookies use `HttpOnly`, `SameSite=Lax`, and `Secure`
  for HTTPS requests.
- Expanded the password column to 255 characters and the confirmation-code
  column to 64 characters.
- Updated the Docker PHP-FPM base image, Memcached extension, Xdebug settings,
  Compose configuration, and PHP 8.5 runtime checks.
- Added archive exclusions and documented migration and verification commands.

### Fixed

- Corrected duplicate-email validation when editing a user.
- Corrected user-photo deletion so the stored file path is retained until the
  file is removed.
- Added explicit runtime and type validation for updated WebSK, container,
  entity, route, and database contracts.

## 2.4.10 - 2026-05-20

- Restored `302 Found` responses for authentication workflow redirects after
  the `307 Temporary Redirect` change in 2.4.9.

## 2.4.9 - 2026-05-20

- Changed redirects in authentication and user-management handlers from
  `302 Found` to `307 Temporary Redirect`.

## 2.4.8 - 2025-11-26

- Updated PHPMailer from 6.5 to 7.0.1.
- Corrected registration-email wording.

## 2.4.7 - 2025-05-18

- Fixed confirmation-registration service usage.
- Simplified rendered-content and password-hash return paths.
- Updated email plain-text sanitization and cleaned up authentication handlers.

## 2.4.6 - 2025-04-22

- Added a schema migration that normalizes null user comments.
- Prevented the registration form from pre-filling name and email values.

## 2.4.5 - 2025-04-21

- Migrated the demo and user-creation CLI to the PHP-DI container builder.
- Added stricter entity property types and corrected CRUD field names and form
  identifiers.
- Added schema normalization for confirmation codes and role fields.
- Documented creation of an administrator from the command line.

## 2.4.4 - 2025-04-20

- Centralized user-photo storage directories and URLs in `UserService`.
- Updated profile forms and photo deletion to use the centralized paths.

## 2.4.3 - 2025-04-16

- Added database migrations that normalize null `created_at_ts`, provider UID,
  and profile URL values.

## 2.4.2 - 2025-04-01

- Updated AJAX list handlers to write through the PSR-7 response body and
  return the response object.

## 2.4.1 - 2025-03-29

- Initialized confirmation codes with an empty-string default.
- Fixed the default session expiration delta.

## 2.4.0 - 2025-03-26

- Updated the WebSK CAPTCHA, console, CRUD, database, Slim, utility, and view
  dependency lines.

## 2.3.0 - 2025-03-23

- Raised the runtime baseline to PHP 8.3 and updated the dependency set.
- Added the Docker development environment.
- Added typed properties, parameters, and return values across the package.
- Removed the legacy HybridAuth/social-login flow and obsolete upload handlers.

## 2.2.2 - 2023-02-05

- Fixed `currentUserHasAccessByAnyRoleDesignations()` so access is granted when
  the current user has any requested role.

## 2.2.1 - 2023-02-05

- Added `currentUserHasAccessByAnyRoleDesignations()`.
- Added return types and stricter typing to session authorization methods.

## 2.2.0 - 2022-02-13

- Updated the WebSK cache, CAPTCHA, CRUD, entity, Slim, and view dependencies.
- Added a string return type to the authentication facade accessor.

## 2.1.8 - 2021-07-22

- Migrated request handlers and middleware from Slim request/response classes
  to PSR-7 `ServerRequestInterface` and `ResponseInterface`.

## 2.1.7 - 2021-05-22

- Fixed Composer `bin` placement and simplified CLI configuration validation.

## 2.1.6 - 2021-05-22

- Clarified automatic/manual migration and user-creation commands in README.

## 2.1.5 - 2021-05-22

- Tag points to the same commit as 2.1.3 and 2.1.4; no additional source
  changes are present.

## 2.1.4 - 2021-05-22

- Tag points to the same commit as 2.1.3 and 2.1.5; no additional source
  changes are present.

## 2.1.3 - 2021-05-22

- Added the `auth:create_user` CLI command and Composer binary.
- Added the console dependency and administrator-role assignment for created
  users.

## 2.1.2 - 2021-05-22

- Renamed database foreign-key constraints to unique table-specific names.

## 2.1.1 - 2021-05-17

- Replaced the monolithic SQL dump with incremental database migrations.
- Added the dump path to database configuration and documented migration
  commands.

## 2.1.0 - 2021-04-14

- Updated the WebSK CRUD, database, and entity dependencies.

## 2.0.10 - 2021-02-07

- Updated the package and demo for PHP 7.4.
- Refined registration checks and reorganized demo application files.

## 2.0.9 - 2020-12-17

- Corrected the registration-user validation introduced in 2.0.8.

## 2.0.8 - 2020-12-17

- Added validation for already registered users.

## 2.0.7 - 2020-12-17

- Fixed the PHPMailer namespace and updated Composer dependencies.

## 2.0.6 - 2020-08-11

- Corrected the role-ID lookup introduced in 2.0.5.

## 2.0.5 - 2020-08-11

- Added repository and service support for retrieving user IDs by role ID.

## 2.0.4 - 2020-07-23

- Updated PHPMailer and its locked dependency set.

## 2.0.3 - 2020-05-18

- Cleaned up registration and user-edit handlers and templates.

## 2.0.2 - 2020-05-18

- Updated the SQL schema and user/role administration behavior.
- Increased generated password length and fixed role creation.

## 2.0.1 - 2019-10-15

- Fixed the forgot-password flow.
- Updated session handling and demo application documentation.

## 2.0.0 - 2019-09-17

- Refactored authentication, sessions, roles, users, routes, repositories, and
  CRUD handlers.
- Renamed the `Users` namespace to `User` and introduced dedicated service and
  configuration classes.

## 1.1.1 - 2019-07-06

- Added the standalone demo application, default configuration, SQL dump,
  public entry point, and layouts.
- Fixed role persistence in the demo stack.

## 1.0.12 - 2019-07-05

- Cleaned up the login form and user-save handler.

## 1.0.11 - 2019-06-17

- Fixed confirmation-registration and user helper behavior.

## 1.0.10 - 2019-06-14

- Fixed authentication route definitions.

## 1.0.9 - 2019-06-14

- Fixed authentication route definitions.

## 1.0.8 - 2019-06-14

- Fixed authentication service behavior.

## 1.0.7 - 2019-06-14

- Added social-login UI and related authentication handling.
- Corrected the registration form action.

## 1.0.6 - 2019-04-24

- Updated user and role list styles.
- Added `autocomplete="new-password"` to the user form.

## 1.0.5 - 2019-04-07

- Updated the user entity and locked dependency set.

## 1.0.4 - 2019-04-06

- Fixed user utilities, role-list rendering, and Composer metadata.

## 1.0.3 - 2019-03-13

- Added access to the current user object in the authentication facade.

## 1.0.2 - 2019-03-04

- Migrated forms and administration handlers to module-namespace template
  rendering.

## 1.0.1 - 2019-02-19

- Initial release with authentication routes, login, registration, password
  recovery, session management, users, roles, repositories, services, and
  administration views.

## Remote release notes

- Remote tags `2.0.5`, `2.1.3`, `2.1.4`, `2.1.5`, `2.1.6`, and `2.2.1`
  exist without corresponding GitHub Release objects.
- Tags `2.1.3`, `2.1.4`, and `2.1.5` all point to commit `ea80dcb`.
- GitHub Release bodies are empty except for 2.0.4, whose body states
  `phpmailer update`.

[Unreleased]: https://github.com/websk/php-auth/compare/3.0.0...HEAD
[3.0.0]: https://github.com/websk/php-auth/compare/2.4.10...3.0.0
