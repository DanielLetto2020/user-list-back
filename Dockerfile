#FROM php:8.1.10-fpm-alpine3.16
#ARG user
#ARG uid
#
#RUN apt-get update && apt-get install -y software-properties-common
#RUN LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php
#RUN apt-get update
#
#RUN apt-get install -y \
#    git \
#    curl \
#    libpq-dev \
#    libpng-dev \
#    libonig-dev \
#    libxml2-dev \
#    libzip-dev \
#    libxslt-dev \
#    php8-soap \
#    libfreetype6-dev \
#    libjpeg62-turbo-dev \
#    libpng-dev \
#    libwebp-dev \
#    zip \
#    unzip \
#    libicu-dev
#
#RUN apt-get clean && rm -rf /var/lib/apt/lists/*
#
#RUN docker-php-ext-install soap
#RUN docker-php-ext-enable soap
#RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
#RUN docker-php-ext-install gd pdo_mysql mbstring exif pcntl bcmath xsl zip
#
#COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
#RUN useradd -G www-data,root -u $uid -d /home/$user $user
#RUN mkdir -p /home/$user/.composer && \
#    chown -R $user:$user /home/$user
#WORKDIR /oracal_online_back
#USER $user

FROM php:8.1.0-fpm

ARG user
ARG uid

#ENV TZ=UTC
ENV TZ=Europe/Moscow

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && apt-get install -y apt-utils wget cmake gcc build-essential \
    gnupg gosu curl ca-certificates zip unzip git supervisor \
    libonig-dev libcap2-bin libpng-dev libjpeg62-turbo-dev \
    libfreetype6-dev zlib1g-dev libicu-dev libxml2-dev libzip-dev libldb-dev \
    libldap2-dev libcurl4-openssl-dev libpq-dev libxslt-dev libwebp-dev \
    && apt-get update \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-install soap
RUN docker-php-ext-enable soap
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install gd pdo_mysql mbstring exif pcntl bcmath xsl zip intl xml ldap curl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

WORKDIR /user-list-back

USER $user
