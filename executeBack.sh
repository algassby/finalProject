#!/bin/bash
cd Back

docker-compose exec -T app php bin/console doctrine:migration:migrate
docker-compose exec -T app php bin/console doctrine:fixtures:load
docker-compose exec -T app php bin/console doctrine:database:drop --force --env=test
docker-compose exec -T app php bin/console doctrine:database:create --env=test
docker-compose exec -T app php bin/console doctrine:migrations:migrate -n --env=test
docker-compose exec -T app ./vendor/bin/phpunit
docker exec -e XDEBUG_MODE=coverage php-app vendor/bin/phpunit --coverage-text
