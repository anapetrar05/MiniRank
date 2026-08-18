# MiniRank: single container running Apache + PHP with the SQLite database
# stored on a persistent volume. SQLite is embedded, so no separate DB
# container is required.
FROM php:8.2-apache

# Apache document root must be the app's public/ directory.
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf && a2enmod rewrite

# Working directory + app code.
WORKDIR /var/www/html
COPY . .

# The data/ directory (SQLite lives here) must be writable by the Apache user.
RUN mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html

# PHPUnit runner (11.x is the latest line supporting PHP 8.2) so tests can be
# executed inside the container.
RUN php -r "copy('https://phar.phpunit.de/phpunit-11.5.phar', 'phpunit.phar');"

# On every start: seed the database if it is empty (idempotent), make sure the
# SQLite file is owned by Apache, then run Apache in the foreground.
CMD ["sh", "-c", "php scripts/seed.php; chown -R www-data:www-data /var/www/html/data; exec apache2-foreground"]
