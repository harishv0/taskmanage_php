# Use the official PHP image
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies if using Composer
RUN apt-get update && apt-get install -y unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev

# Expose the default port
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
