# Portfolio API - Symfony

API minimaliste créée avec Symfony 7.1 et Docker.

## Prérequis

- Docker Desktop installé et démarré
- Docker Compose

## Installation

1. **Démarrer Docker Desktop** (important avant de lancer les commandes)

2. **Lancer les conteneurs Docker :**
```bash
docker-compose up -d
```

3. **Installer les dépendances Composer :**
```bash
docker-compose exec php composer install
```

4. **Créer la base de données :**
```bash
docker-compose exec php php bin/console doctrine:database:create
```

## Accès

- **API :** http://localhost:8080/api
- **Health check :** http://localhost:8080/api/health
- **Base de données PostgreSQL :** localhost:5432
  - Database: `portfolio`
  - User: `portfolio`
  - Password: `portfolio`

## Commandes utiles

### Arrêter les conteneurs
```bash
docker-compose down
```

### Voir les logs
```bash
docker-compose logs -f
```

### Accéder au conteneur PHP
```bash
docker-compose exec php bash
```

### Console Symfony
```bash
docker-compose exec php php bin/console
```

### Créer une migration
```bash
docker-compose exec php php bin/console make:migration
docker-compose exec php php bin/console doctrine:migrations:migrate
```

## Structure

- `src/Controller/` - Contrôleurs de l'API
- `src/Entity/` - Entités Doctrine
- `config/` - Configuration Symfony
- `docker/` - Configuration Docker
- `public/` - Point d'entrée web

## Configuration CORS

Le CORS est configuré pour accepter toutes les requêtes sur `/api/*`. Modifiez `config/packages/nelmio_cors.yaml` selon vos besoins.
