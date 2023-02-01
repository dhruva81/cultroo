#!/bin/sh

set -e
echo "Deploying application ....."

# Enter maintenance mode
sudo php artisan down 
     # Update codebase
    # git fetch origin main
    # git reset --hard origin/main

    # Install dependencies based on lock file

    sudo composer install --no-interaction --prefer-dist --optimize-autoloader
    # Migrate database
    sudo php artisan migrate --force

# Note: If you're using queue workers, this is the place to restart them.
# ...

# Clear cache

    sudo php artisan optimize

    # Reload PHP to update opcache
    echo "" | sudo -S service php8.1-fpm reload

# Exit maintenance mode

sudo php artisan up

echo "Application deployed!!"
