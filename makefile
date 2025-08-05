DOCKER_COMPOSE=docker compose -f docker/docker-compose.yml
PHP_SERVICE=php
PROJECT_DIR=/var/www/html

wait-for-db:
	@echo "Waiting for DB..."
	@until docker exec product-api-php php -r "try { new PDO('mysql:host=db;dbname=app', 'symfony', 'symfony'); } catch (Exception $${e}) { exit(1); }"; do \
		sleep 1; \
	done
	@echo "DB ready!"

up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

logs:
	$(DOCKER_COMPOSE) logs -f

composer-install:
	$(DOCKER_COMPOSE) exec -T $(PHP_SERVICE) composer install

db-create: wait-for-db
	$(DOCKER_COMPOSE) exec -T $(PHP_SERVICE) php $(PROJECT_DIR)/bin/console doctrine:database:create --if-not-exists

migrate:
	$(DOCKER_COMPOSE) exec -T $(PHP_SERVICE) php $(PROJECT_DIR)/bin/console doctrine:migrations:migrate --no-interaction

messenger-setup:
	$(DOCKER_COMPOSE) exec -T $(PHP_SERVICE) php $(PROJECT_DIR)/bin/console messenger:setup-transports

messenger-consume:
	$(DOCKER_COMPOSE) exec -T $(PHP_SERVICE) php $(PROJECT_DIR)/bin/console messenger:consume async -vv

git-safe-dir:
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) git config --global --add safe.directory $(PROJECT_DIR)

messenger-worker-up:
	$(DOCKER_COMPOSE) up -d messenger_worker

messenger-worker-logs:
	$(DOCKER_COMPOSE) logs -f messenger_worker

messenger-worker-down:
	$(DOCKER_COMPOSE) stop messenger_worker

setup: up git-safe-dir composer-install db-create migrate messenger-setup messenger-worker-up

test:
	$(DOCKER_COMPOSE) exec -e APP_ENV=test $(PHP_SERVICE) ./vendor/bin/phpunit -c phpunit.dist.xml

