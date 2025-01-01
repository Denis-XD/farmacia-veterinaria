FROM php:7.4-fpm

WORKDIR /var/www/html

ENV APP_NAME="Farmacia veterinaria"
ENV APP_ENV="local"
ENV APP_KEY="base64:Symf9smyXUFvo1MwzIzzAQYsAGfwB/3oG4GkmzsHkaw="
ENV APP_DEBUG="true"
ENV APP_URL="https://farmacia-veterinaria-production.up.railway.app"
ENV LOG_CHANNEL="stack"
ENV DB_CONNECTION="mysql"
ENV DB_HOST="mysql.railway.internal"
ENV DB_PORT="3306"
ENV DB_DATABASE="railway"
ENV DB_USERNAME="root"
ENV DB_PASSWORD="saNwDmoowkYflxcUKkNVdzyIwRHTxgYe"
ENV BROADCAST_DRIVER="log"
ENV CACHE_DRIVER="file"
ENV QUEUE_CONNECTION="sync"
ENV SESSION_DRIVER="file"
ENV SESSION_LIFETIME="420"
ENV REDIS_HOST="127.0.0.1"
ENV REDIS_PASSWORD="null"
ENV REDIS_PORT="6379"
ENV MAIL_MAILER="smtp"
ENV MAIL_HOST="smtp.gmail.com"
ENV MAIL_PORT="587"
ENV MAIL_USERNAME="palmertechn@gmail.com"
ENV MAIL_PASSWORD="exkbuzrnohzesozl"
ENV MAIL_ENCRYPTION="tls"
ENV MAIL_FROM_ADDRESS="palmertechn@gmail.com"
ENV MAIL_FROM_NAME="${APP_NAME}"
ENV PUSHER_APP_ID=""
ENV PUSHER_APP_KEY=""
ENV PUSHER_APP_SECRET=""
ENV PUSHER_APP_CLUSTER="mt1"
ENV MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
ENV MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

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
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install zip \
    && docker-php-source delete


COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

RUN php artisan optimize && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan migrate --force && \
    php artisan db:seed --class=DatabaseSeeder --force && \
    php artisan storage:link
    

EXPOSE 9000