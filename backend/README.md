# Backend - TP VueJS WR406D

## Démarrage avec Docker

```bash
cd backend
docker-compose up -d
docker-compose exec php composer install
docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

L'API sera disponible sur: http://localhost:8080/api
Documentation Swagger: http://localhost:8080/api/docs

## Endpoints disponibles

- GET/POST  /api/vehicules
- GET/PUT/PATCH/DELETE /api/vehicules/{id}
- GET/POST  /api/marques
- GET/PUT/PATCH/DELETE /api/marques/{id}
