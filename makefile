DOCKER_COMPOSE=docker compose -f docker/docker-compose.yml
PHP_SERVICE=php
PROJECT_DIR=/var/www/html

up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

logs:
	$(DOCKER_COMPOSE) logs -f

composer-install:
	$(DOCKER_COMPOSE) exec -T $(PHP_SERVICE) composer install

db-create:
	$(DOCKER_COMPOSE) exec -T $(PHP_SERVICE) php $(PROJECT_DIR)/bin/console doctrine:database:create --if-not-exists

migrate:
	$(DOCKER_COMPOSE) exec -T $(PHP_SERVICE) php $(PROJECT_DIR)/bin/console doctrine:migrations:migrate --no-interaction

git-safe-dir:
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) git config --global --add safe.directory $(PROJECT_DIR)

setup: up git-safe-dir composer-install db-create migrate

test:
	$(DOCKER_COMPOSE) exec -e APP_ENV=test $(PHP_SERVICE) ./vendor/bin/phpunit -c phpunit.dist.xml
