FROM php:8.2-cli

# Enable mysqli extension
RUN docker-php-ext-install mysqli

# Copy project files
COPY . /app

# Set proper permissions
RUN chown -R www-data:www-data /app/public/avatars

WORKDIR /app

EXPOSE 80

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-80} -t /app"]
