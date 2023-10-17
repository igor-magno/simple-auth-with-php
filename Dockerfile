FROM php:8.2.0-apache

RUN apt-get update && \
    apt-get install -y libxml2-dev

# RUN a2enmod rewrite

RUN docker-php-ext-install pdo pdo_mysql

# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# RUN apt-get update && apt-get install -y git

# WORKDIR /var/www

# RUN rm -rf html

# RUN ln -s /var/www/app/src/Infra/Public html 

# COPY . .

# ENV COMPOSER_ALLOW_SUPERUSER 1

# RUN composer config -g github-oauth.github.com GITHUB_TOKEN

# RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader --no-scripts
