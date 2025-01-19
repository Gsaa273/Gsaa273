# Use official PHP image with Apache
FROM php:8.2-apache

# Install dependencies for PHP extensions and Composer
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy your application files (including composer.json)
COPY . .

# Install PHP dependencies listed in composer.json
RUN composer install --no-interaction --optimize-autoloader

# Expose Apache port
EXPOSE 80

# Run Apache in the foreground
CMD ["apache2-foreground"]
