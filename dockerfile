# Imagen base para PHP con FPM
FROM php:8.2-fpm

# Instalar dependencias necesarias para Laravel
RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www

# Copiar archivos de la aplicación
COPY . .

# Instalar dependencias de Laravel
RUN composer install --ignore-platform-reqs

# Establecer permisos para directorios críticos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 777 /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 777 /var/www/bootstrap/cache

RUN echo "upload_max_filesize=200000000M" >> /usr/local/etc/php/php.ini && \
    echo "post_max_size=200000000M" >> /usr/local/etc/php/php.ini && \
    echo "memory_limit=10G" >> /usr/local/etc/php/php.ini


# Exponer el puerto para PHP-FPM
EXPOSE 8000

# Comando por defecto
CMD ["php","artisan","serve","--host=0.0.0.0","--port=80"]
