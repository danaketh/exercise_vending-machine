# Start with the official PHP 8.2 FPM image
FROM php:8.2-fpm

# Install system dependencies for Composer and Symfony
RUN apt-get update && \
    apt-get install -y \
        git \
        unzip \
        wget && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install Composer globally
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Install Symfony CLI globally
RUN wget https://get.symfony.com/cli/installer -O - | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Use the default PHP FPM start command
CMD ["php-fpm"]
