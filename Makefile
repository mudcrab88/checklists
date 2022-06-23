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

test:
	@docker-compose exec -T app /app/vendor/bin/phpunit --testdox -v /app/tests

rbac-migrate:
	@docker-compose exec -T app php yii migrate --migrationPath=@yii/rbac/migrations

rbac-init:
	@docker-compose exec -T app php yii rbac/init