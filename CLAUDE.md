# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Symfony 8.0 PHP web application (PHP 8.4+) for a library/books management system. Early-stage project with scaffolding in place but minimal business logic. Uses Docker for local development with MySQL 8.0.

## Development Environment

```bash
# Start services (app on :8080, MySQL on :3306, MailPit on :8025)
docker compose up

# Run Symfony console commands inside the container
docker compose exec app php bin/console [command]

# Install PHP dependencies
docker compose exec app composer install
```

## Testing

```bash
# Run all tests (PHPUnit 12.5, strict mode)
docker compose exec app ./bin/phpunit

# Run a single test file
docker compose exec app ./bin/phpunit tests/Path/To/TestFile.php

# Run a specific test method
docker compose exec app ./bin/phpunit --filter testMethodName
```

PHPUnit is configured in `phpunit.dist.xml` with strict settings: fails on deprecations, warnings, and notices.

## Architecture

- **Framework:** Symfony 8.0 with MicroKernelTrait (`src/Kernel.php`)
- **ORM:** Doctrine 3.6 — entities in `src/Entity/`, repositories in `src/Repository/`
- **Database:** MySQL 8.0, connection configured via `DATABASE_URL` env var
- **Templates:** Twig (`templates/`)
- **Frontend:** Symfony Asset Mapper with Hotwired Stimulus + Turbo (`assets/`)
- **Message Queue:** Doctrine transport (`config/packages/messenger.yaml`)
- **Migrations:** `migrations/` directory, managed via `doctrine:migrations:*` commands

## Configuration

- Environment files: `.env` (defaults), `.env.dev` (dev secrets), `.env.test` (test config), `.env.local` (gitignored overrides)
- Bundle configs: `config/packages/` (doctrine, security, mailer, etc.)
- Services: `config/services.yaml` (autowiring enabled, `src/` auto-registered)
- Routes: `config/routes.yaml` + `config/routes/`

## Code Style

- UTF-8, 4-space indentation, LF line endings (`.editorconfig`)
