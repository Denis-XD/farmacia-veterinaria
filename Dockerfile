# Usa la imagen oficial de PHP 7.4
FROM php:7.4-fpm

# Instala Node.js y NPM
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - && \
    apt-get install -y nodejs

# Instala las extensiones necesarias de PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo pdo_mysql

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura el directorio de trabajo
WORKDIR /var/www/html

# Copia el código del proyecto
COPY . .

# Instala las dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Da permisos a los directorios necesarios
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copia el script de despliegue al contenedor
COPY .docker/deploy.sh /usr/local/bin/deploy.sh

# Haz que el script sea ejecutable
RUN chmod +x /usr/local/bin/deploy.sh

# Expone el puerto 9000 (PHP-FPM)
EXPOSE 9000

# Ejecuta el script de despliegue al iniciar el contenedor
CMD ["bash", "-c", "/usr/local/bin/deploy.sh && php-fpm"]
