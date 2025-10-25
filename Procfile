# Heroku Configuration
# This file configures Heroku deployment for Laravel

# Buildpack
heroku/php

# Environment variables
APP_ENV=production
APP_DEBUG=false
APP_URL=https://diario-obras-app.herokuapp.com
DB_CONNECTION=pgsql
DB_HOST=${{DATABASE_URL}}
DB_PORT=5432
DB_DATABASE=${{DATABASE_NAME}}
DB_USERNAME=${{DATABASE_USER}}
DB_PASSWORD=${{DATABASE_PASSWORD}}

# Build commands
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start command
php artisan serve --host=0.0.0.0 --port=$PORT

