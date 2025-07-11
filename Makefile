DOCKER := $(shell which docker)
COMPOSE := $(DOCKER) compose
WITH_DOCKER ?= 1

ifeq ($(WITH_DOCKER), 1)
	PHP=$(COMPOSE) run --rm php
	NPM=$(COMPOSE) run --rm front

	PHPCS_CMD=$(PHP) sh -c 'PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix --allow-risky=yes'
	PHPCS_CMD_DRY=$(PHP) sh -c 'PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix --dry-run --allow-risky=yes'
else
	PHP=cd api &&
	NPM=cd front &&

	PHPCS_CMD=export PHP_CS_FIXER_IGNORE_ENV=1 && $(PHP) vendor/bin/php-cs-fixer fix --allow-risky=yes
endif

CONSOLE := $(PHP) bin/console

up:
	$(COMPOSE) up -d

stop:
	$(COMPOSE) stop

install:
	echo "Installing dependencies..."
	$(PHP) composer install
	$(NPM) npm install
	@mkdir -p api/public/uploads/game

dev: install up


############################## CODE STYLE ##############################
phpcs:
	$(PHPCS_CMD)

phpcs-dry:
	$(PHPCS_CMD_DRY)

jscs:
	$(NPM) npm run format

jscs-dry:
	$(NPM) npm run format-dry

############################## LINTING ##############################

symfony-lint:
	$(CONSOLE) lint:container

vue-lint:
	$(NPM) npm run lint
	$(NPM) npm run type-check

############################## BUILD ##############################

front-build:
	$(NPM) npm run build-only

############################## Test ##############################

pretests:
	$(CONSOLE) -etest d:d:d --if-exists --force
	$(CONSOLE) -etest d:d:c --if-not-exists
	$(CONSOLE) -etest d:s:u -f
	$(CONSOLE) -etest lexik:jwt:generate-keypair --overwrite -n
	$(CONSOLE) -etest app:sync:data

tests:
	$(PHP) bin/phpunit tests/ --fail-on-warning --fail-on-deprecation --testdox

############################## Setup Symfony ##############################

db:
	$(CONSOLE) doctrine:database:create --if-not-exists
	$(CONSOLE) doctrine:schema:update --force

drop-db:
	$(CONSOLE) doctrine:database:drop --force --if-exists

fixtures: db
	$(CONSOLE) doctrine:fixtures:load --no-interaction

db-data: db
	$(CONSOLE) app:sync:data
