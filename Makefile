DOCKER=docker run -u root --rm
DOCKER_KEEP=docker run -u root

COMPOSER_DOCKER_IMAGE=composer

COMPOSER=$(DOCKER) -v ${PWD}:/app $(COMPOSER_DOCKER_IMAGE)

.PHONY: install
install: ## Install project dependencies
	@$(COMPOSER) composer install && composer dump-autoload

.PHONY: web-shell
web-shell: ## Open the shell of the web container
	@docker exec -it web_container bash

.PHONY: code-std
code-std: ## Standardize the PHP code according to PSR2
	@docker exec -it web_container ./vendor/bin/phpcbf

.PHONY: code-chk
code-chk: ## Check the PHP code according to PSR2
	@docker exec -it web_container ./vendor/bin/phpcs

.PHONY: safe-chk
safe-chk: ## Check if dependencies are safe
	@docker exec -it web_container ./bin/console security:check

.PHONY: db-clean
db-clean: ## Creates a new database and load the fixtures
	@docker exec -it web_container ./bin/console doctrine:database:drop --force
	@docker exec -it web_container ./bin/console doctrine:database:create
	@docker exec -it web_container ./bin/console doctrine:migrations:migrate --no-interaction
	@docker exec -it web_container ./bin/console doctrine:fixtures:load --no-interaction || true

.PHONY: run
run: ## run the application
	@mkdir -p data
	@docker-compose up -d
	# Initializing containers (30 Sec) ...
	@sleep 30
	@docker exec -it web_container ./bin/console doctrine:migrations:migrate --no-interaction
	@docker exec -it web_container ./bin/console doctrine:fixtures:load --no-interaction || true

.PHONY: clean
clean: ## stops the containers if exists and remove all the dependencies
	@docker-compose down --remove-orphans || true
	@rm -rf data
	@rm -rf vendor

.PHONY: help
help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.DEFAULT_GOAL := help
