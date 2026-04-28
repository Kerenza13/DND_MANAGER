FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    curl git unzip libicu-dev \
    && docker-php-ext-install pdo pdo_mysql intl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN mkdir -p var/cache var/log \
    && composer install --no-interaction --optimize-autoloader \
    && chmod -R 777 var

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public", "public/index.php"]
