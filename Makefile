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
	@echo "  console   Run bin/console, e.g. 'make console ARGS=cache:clear'"
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

console:
	docker compose exec -T php php bin/console $(ARGS)

test:
	docker compose exec -T php ./vendor/bin/simple-phpunit $(ARGS)
