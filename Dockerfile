# Usa la imagen oficial de PHP 7.4
FROM php:7.4-fpm

# Instala las extensiones necesarias de PHP
RUN apt update && apt install -y \
    nano \
    nodejs \
    npm \
    libpng-dev \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    libpq-dev \
    zip \
    curl \
    unzip \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install zip \
    && docker-php-source delete

COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.con
# Configura el directorio de trabajo
WORKDIR /var/www/html

# Copia el código del proyecto
COPY . .

# Instala las dependencias de Laravel
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

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
