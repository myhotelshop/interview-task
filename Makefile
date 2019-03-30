DOCKER=docker run -u root --rm
DOCKER_KEEP=docker run -u root

COMPOSER_DOCKER_IMAGE=composer

COMPOSER=$(DOCKER) -v ${PWD}:/app $(COMPOSER_DOCKER_IMAGE)

install: ## Install dependencies
	@$(COMPOSER) composer install && composer dump-autoload

web-shell: ## Open the shell of the web container
	@docker exec -it web_container bash

code-std: ## Standardize the PHP code according to PSR2
	@docker exec -it web_container ./vendor/bin/phpcbf

code-chk: ## Check the PHP code according to PSR2
	@docker exec -it web_container ./vendor/bin/phpcs

safe-chk: ## Check if dependencies are safe
	@docker exec -it web_container ./bin/console security:check

run: ## run the application
	@docker-compose up -d

kill: ## stops the web container and deletes it
	@docker-compose down --remove-orphans

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.DEFAULT_GOAL := help
