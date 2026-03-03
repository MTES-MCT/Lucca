# Lucca v2

![Lucca logo](public/assets/logo/lucca-color.png)

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework, with full HTTP/2, HTTP/3 and HTTPS support.

## Architecture & DevOps

Lucca uses a containerized architecture based on three main services:
* **PHP (php-fpm)**: The Symfony core (PHP 8.4) including Composer and wkhtmltopdf.
* **Caddy**: A modern web server acting as a reverse proxy and managing HTTPS.
* **Database**: MariaDB server (version 11.8.1).

### Environment Configuration
The project relies on a hierarchy of `.env` files:
* **.env**: Default parameters (Host, DB, Storage paths in `/srv/docs/`).
* **.env.dev / .env.prod**: Environment-specific settings.
* **.env.local**: Local overrides (**non-versioned**), used for secrets like API keys and passwords.

## Getting Started - Local Dev

1. Run `docker-compose up -d`.
2. Run `docker exec -it lucca_php bash`.
    1. Run `COMPOSER_MEMORY_LIMIT=-1 composer install`.
    2. Run `php bin/console fos:js-routing:dump --format=json --target=assets/routes.json`.
    3. Run `php bin/console asset-map:compile`.
    4. Run `php bin/console lucca:init:media`.
    5. Run `php bin/console lucca:init:department`.
    6. Run `php bin/console doctrine:migrations:migrate`.
3. Create a new user and a new adherent in the database.
4. Add `lucca.local` to your OS host file pointing to `127.0.0.1`.
5. Open `https://localhost:8027` (Port defined in the `docker-compose.override.yml`).

### Infrastructure Nuances (Legacy)
The project contains specific references to the **Numeric Wave** infrastructure:
* **Network**: The project uses an external network named `nw-lan-dev`. If it does not exist, create it: `docker network create nw-lan-dev`.
* **Volumes**: A mount point to `../lucca-v2.doc` is present in `docker-compose.yml` for media storage.
* **Ports**: In development, MariaDB is exposed on port `33066`.

## Launch in Production

To launch the project in production, use the specific configuration file:

1. Define your environment variables (`APP_SECRET`, `MYSQL_PASSWORD`, etc.) in your system or a `.env.local` file.
2. Execute:
   ```bash
   docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
   ```
3. Containers will have the `_prod` suffix (e.g., `php_lucca_prod`) and use an isolated internal network `lucca-network`.

## Migrations
- Create a new migration: `php bin/console doctrine:migrations:diff`
- Apply migrations: `php bin/console doctrine:migrations:migrate`
- Revert: `php bin/console doctrine:migrations:migrate prev`

## Unit Testing
Run unit tests with memory limit bypass:
- `php -d memory_limit=-1 bin/phpunit src`
- Specific bundle: `php -d memory_limit=-1 bin/phpunit src/Lucca/Bundle/DepartmentBundle`

## Docs

1. [Initialization project](docs/initialization_lucca.md)
2. [Environment vars](docs/env_vars.md)

**Lucca Commands:**

* Change a user password: `lucca:user:change-password`
* Unban specific IP address: `lucca:security:unban`

## Credits

Created by [Numeric Wave](https://numeric-wave.eu).
