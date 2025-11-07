# Use official PHP 8.2 image with Apache
FROM php:8.2-apache

# Install required system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html
COPY . /var/www/html
WORKDIR /var/www/html/public

# Tell Apache to serve the Laravel public directory
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Copy project files into container
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install dependencies (non-interactive mode)
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader || true
# Set file permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 777 /var/www/html/storage
RUN chmod -R 777 /var/www/html/bootstrap/cache

# Tell Apache to serve the Laravel public directory
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Expose port 80 for Render
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]