FROM php:8.2-apache

# Enable mysqli extension
RUN docker-php-ext-install mysqli

# Copy project files to Apache's web root
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/public/avatars

EXPOSE 80
