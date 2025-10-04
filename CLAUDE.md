# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Symfony 7.1 REST API for a portfolio website. The application runs in Docker containers with PHP, Nginx, and PostgreSQL.

## Architecture

- **Framework**: Symfony 7.1 (PHP 8.3+)
- **Database**: PostgreSQL 16
- **ORM**: Doctrine
- **API Documentation**: Nelmio API Doc (OpenAPI/Swagger)
- **CORS**: Nelmio CORS Bundle (configured to accept all requests on `/api/*`)

### Directory Structure

- `src/Controller/` - API controllers with OpenAPI attributes
- `src/Entity/` - Doctrine entities (mapped via PHP attributes)
- `config/packages/` - Bundle configurations
- `docker/` - Docker configuration files (PHP Dockerfile, Nginx config)

## Development Commands

All commands must be run inside the PHP container. Prefix with `docker-compose exec php` or enter the container with `docker-compose exec php bash`.

### Starting the Application

```bash
docker-compose up -d
docker-compose exec php composer install
docker-compose exec php php bin/console doctrine:database:create
```

### Symfony Console

```bash
docker-compose exec php php bin/console [command]
```

### Database Operations

```bash
# Create database
docker-compose exec php php bin/console doctrine:database:create

# Create migration
docker-compose exec php php bin/console make:migration

# Run migrations
docker-compose exec php php bin/console doctrine:migrations:migrate

# Drop and recreate database (dev only)
docker-compose exec php php bin/console doctrine:database:drop --force
docker-compose exec php php bin/console doctrine:database:create
docker-compose exec php php bin/console doctrine:migrations:migrate
```

### Docker Operations

```bash
# View logs
docker-compose logs -f

# Stop containers
docker-compose down

# Access PHP container
docker-compose exec php bash
```

## API Documentation

- API endpoints are documented using OpenAPI attributes (`#[OA\...]`) directly in controllers
- Access Swagger UI at: http://localhost:8080/api/doc (if SwaggerUiController is configured)
- API base URL: http://localhost:8080/api
- Health check: http://localhost:8080/api/health

## Database Access

- **Host**: localhost:5432
- **Database**: portfolio
- **User**: portfolio
- **Password**: portfolio

## Configuration Notes

- CORS is configured in `config/packages/nelmio_cors.yaml` to accept all origins on `/api/*` paths
- Database connection URL is set via `DATABASE_URL` environment variable
- Doctrine entities use attribute mapping (not XML/YAML)
- API routes are prefixed with `/api` (see `ApiController`)
