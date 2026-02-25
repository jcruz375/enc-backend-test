FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    zip unzip git curl bash \
    && rm -rf /var/lib/apt/lists/*

# Redis
RUN pecl install redis && docker-php-ext-enable redis

# Configura e instala extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    bcmath exif gd mbstring opcache pcntl pdo_mysql zip

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Copia o código
COPY . .

# Instala dependências
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Corrige permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# SCRIPT FLEXÍVEL PARA TODOS OS SERVIÇOS
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
