# Makefile

.PHONY: up down restart rebuild install key migrate seed docs init reset fresh

# Запуск всех контейнеров
up:
	docker-compose up -d

# Остановка всех контейнеров
down:
	docker-compose down

# Перезапуск всех контейнеров (без пересборки)
restart:
	docker-compose restart

# Пересборка всех контейнеров (с установкой заново)
rebuild:
	docker-compose down --volumes --remove-orphans
	docker-compose build --no-cache
	docker-compose up -d

# Генерация ключа Laravel
key:
	docker-compose exec app php artisan key:generate

# Миграции
migrate:
	docker-compose exec app php artisan migrate

# Сидирование
seed:
	docker-compose exec app php artisan db:seed

# Генерация Swagger-документации
docs:
	docker-compose exec app php artisan l5-swagger:generate

# Полный разворот проекта
init: up wait-composer key migrate seed docs

# Полный сброс базы и сидирование заново
reset:
	docker-compose exec app php artisan migrate:fresh --seed

# Только миграции и сидирование
fresh: migrate

wait-composer:
	@echo "Ожидание установки зависимостей Composer..."
	@until docker-compose exec app test -f vendor/autoload.php; do \
		echo "Ждём завершения composer install..."; \
		sleep 2; \
	done
	@echo "✅ Composer install выполнен!"