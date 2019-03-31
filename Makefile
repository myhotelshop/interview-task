DOCKER=docker run -u root --rm
DOCKER_KEEP=docker run -u root

COMPOSER_DOCKER_IMAGE=composer

COMPOSER=$(DOCKER) -v ${PWD}:/app $(COMPOSER_DOCKER_IMAGE)

.PHONY: install
install: ## Install dependencies
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

.PHONY: run
run: ## run the application
	@docker-compose up -d

.PHONY: kill
kill: ## stops the web container and deletes it
	@docker-compose down --remove-orphans

.PHONY: help
help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.DEFAULT_GOAL := help
