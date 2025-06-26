# Use a PHP 8.2 base image with Apache
FROM php:8.2-apache

# Install system dependencies
# - GnuPG for encryption
# - Git, zip, unzip for Composer
# - Node.js and npm for frontend assets
RUN apt-get update && apt-get install -y \
    gnupg \
    gosu \
    libgpgme-dev \
    git \
    zip \
    unzip \
    libzip-dev \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Install the PHP GnuPG extension
RUN pecl install gnupg && docker-php-ext-enable gnupg

# Install other common PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Set up Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# === NEW: Copy the Apache virtual host configuration ===
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy your project files into the container
COPY . .

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Install NPM dependencies and build the frontend
RUN npm install
RUN npm run build

# Set correct permissions for storage and bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]

# Expose port 80 for the Apache server
EXPOSE 80
