.PHONY: help reload test

help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  dev_reload   clear cache, reload database schema and load fixtures (only for dev environment)"
	@echo "  test         to launch phpunit tests"

composer:
	composer install --optimize-autoloader

dev_clear:
	app/console cache:clear

dev_doctrine_schema:
	app/console doctrine:database:drop --force
	app/console doctrine:database:create
	app/console doctrine:schema:create
	app/console doctrine:fixtures:load

dev_reload: dev_clear dev_doctrine_schema

test:
	rm -rf app/cache/test
	app/console cache:clear --env=test
	bin/phpunit -c app src
