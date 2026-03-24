UID := $(shell id -u)
GID := $(shell id -g)

export UID
export GID

.DEFAULT_GOAL := help

help:
	@echo "Targets:"
	@echo "  up        Start containers"
	@echo "  down      Stop containers"
	@echo "  build     Build images"
	@echo "  logs      Tail logs"
	@echo "  sh        Shell into PHP container"
	@echo "  composer  Run composer, e.g. 'make composer ARGS=install'"
	@echo "  install   Generate .env.dev secret and install dependencies"
	@echo "  console   Run bin/console, e.g. 'make console ARGS=cache:clear'"
	@echo "  mcp       Run the MCP server over stdio in the PHP container"
	@echo "  test      Run PHPUnit, e.g. 'make test ARGS=--testsuite=unit'"

up:
	docker compose up -d --build

down:
	docker compose down

build:
	docker compose build

logs:
	docker compose logs -f --tail=200

sh:
	docker compose exec php sh

composer:
	docker compose exec -T php composer $(ARGS)

install:
	docker compose exec -T php sh -lc 'if [ ! -f .env.dev ]; then secret=$$(php -r "echo bin2hex(random_bytes(16));"); printf "###> symfony/framework-bundle ###\nAPP_SECRET=%s\n###< symfony/framework-bundle ###\n" "$$secret" > .env.dev; fi'
	docker compose exec -T php composer install

console:
	docker compose exec -T php php bin/console $(ARGS)

mcp:
	@docker compose exec -T php php bin/mcp-server.php

test:
	docker compose exec -T php ./vendor/bin/simple-phpunit $(ARGS)
