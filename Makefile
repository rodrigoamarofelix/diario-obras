# Makefile para Laravel Livewire Breeze com Docker

.PHONY: help build up down restart logs shell composer npm artisan migrate seed horizon

# Variáveis
COMPOSE = docker-compose

help: ## Mostra esta ajuda
	@echo "Comandos disponíveis:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

# Comandos básicos
build: ## Constrói as imagens
	$(COMPOSE) build

up: ## Inicia o ambiente
	$(COMPOSE) up -d

down: ## Para o ambiente
	$(COMPOSE) down

restart: ## Reinicia o ambiente
	$(COMPOSE) restart

logs: ## Mostra os logs
	$(COMPOSE) logs -f

# Comandos para desenvolvimento
shell: ## Abre shell no container PHP
	$(COMPOSE) exec php-fpm sh

composer: ## Executa comandos do Composer
	$(COMPOSE) exec php-fpm composer $(filter-out $@,$(MAKECMDGOALS))

npm: ## Executa comandos do NPM
	$(COMPOSE) exec node npm $(filter-out $@,$(MAKECMDGOALS))

artisan: ## Executa comandos do Artisan
	$(COMPOSE) exec php-fpm php artisan $(filter-out $@,$(MAKECMDGOALS))

# Comandos específicos do Laravel
migrate: ## Executa as migrações
	$(COMPOSE) exec php-fpm php artisan migrate

migrate-fresh: ## Executa migrações do zero
	$(COMPOSE) exec php-fpm php artisan migrate:fresh

seed: ## Executa os seeders
	$(COMPOSE) exec php-fpm php artisan db:seed

migrate-seed: ## Executa migrações e seeders
	$(COMPOSE) exec php-fpm php artisan migrate:fresh --seed

horizon: ## Inicia o Horizon
	$(COMPOSE) exec php-fpm php artisan horizon

horizon-terminate: ## Para o Horizon
	$(COMPOSE) exec php-fpm php artisan horizon:terminate

# Comandos de manutenção
clean: ## Limpa containers, volumes e imagens não utilizados
	docker system prune -f
	docker volume prune -f
	docker image prune -f

backup-db: ## Faz backup do banco de dados
	$(COMPOSE) exec mysql mysqldump -u root -p${DB_ROOT_PASSWORD:-root} ${DB_DATABASE:-laravel} > backup_$(shell date +%Y%m%d_%H%M%S).sql

# Comandos de SSL
ssl-generate: ## Gera certificado SSL
	$(COMPOSE) run --rm certbot certonly --webroot --webroot-path=/var/www/html/.well-known/acme-challenge --email ${CERTBOT_EMAIL:-admin@laravel.local} --agree-tos --no-eff-email -d ${DOMAIN_NAME:-laravel.local}

ssl-renew: ## Renova certificado SSL
	$(COMPOSE) run --rm certbot renew

# Comandos de inicialização
init: ## Inicializa o projeto
	@echo "Inicializando ambiente..."
	@if [ ! -f .env ]; then cp .env.example .env; fi
	$(COMPOSE) up -d --build
	$(COMPOSE) exec php-fpm composer install
	$(COMPOSE) exec php-fpm php artisan key:generate
	$(COMPOSE) exec php-fpm php artisan migrate:fresh --seed
	$(COMPOSE) exec node npm install
	@echo "Ambiente inicializado! Acesse: http://laravel.local"

# Permite passar argumentos para comandos
%:
	@:
