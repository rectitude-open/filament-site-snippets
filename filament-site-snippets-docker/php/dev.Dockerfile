FROM php:8.3-fpm

ENV DOCUMENT_ROOT=/home/wwwroot/filament-site-snippets
ENV NODE_VERSION=20.x

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

ARG TZ=UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Optional: Change the apt source to Tsinghua mirror
RUN sed -i 's#deb.debian.org/debian$#mirrors.tuna.tsinghua.edu.cn/debian#' /etc/apt/sources.list.d/debian.sources

RUN apt-get clean
RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install -y \
    apt-utils \
    vim \
    wget \
    git \
    curl \
    htop \
    cron \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zlib1g-dev \
    libmemcached-dev \
    libzip-dev \
    libpng-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    librdkafka-dev \
    libpq-dev \
    libicu-dev \
    openssh-server \
    zip \
    unzip \
    supervisor \
    sqlite3  \
    nano \
    cron

COPY --from=composer/composer /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION} | bash -
RUN apt-get install -y nodejs

RUN apt-get install -y nginx && \
    rm /etc/nginx/sites-enabled/default && \
    mkdir -p /home/wwwlogs

RUN yes | docker-php-ext-install mysqli && \
    docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/ && \
    docker-php-ext-install pdo_mysql gd zip pcntl bcmath mbstring exif && \
    docker-php-ext-enable opcache && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl

RUN yes | pecl install xdebug && \
    docker-php-ext-enable xdebug

WORKDIR ${DOCUMENT_ROOT}

RUN chown -R www-data:www-data ${DOCUMENT_ROOT}
#RUN php artisan storage:link

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
