init: up migrate rbac-migrate rbac-init

build:
	@docker-compose build

up:
	@docker-compose up -d

down:
	@docker-compose down

bash:
	@docker-compose exec app /bin/bash

composer-update:
	@docker-compose exec -T app composer update -d /app

migrate:
	@docker-compose exec -T app php /app/yii migrate

composer-install:
	@docker-compose exec -T app composer install -d /app --ignore-platform-reqs

rbac-migrate:
	@docker-compose exec -T app php yii migrate --migrationPath=@yii/rbac/migrations

rbac-init:
	@docker-compose exec -T app php yii rbac/init

fixtures-load: fixtures-load-user fixtures-load-checklist fixtures-load-item

fixtures-load-user:
	@docker-compose exec -T app php yii fixture/load User

fixtures-load-checklist:
	@docker-compose exec -T app php yii fixture/load Checklist

fixtures-load-item:
	@docker-compose exec -T app php yii fixture/load ChecklistItem

tests-run:
	php vendor/bin/codecept run api

