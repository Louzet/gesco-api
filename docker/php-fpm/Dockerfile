FROM php:7.4-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    wget \
    fish

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y libzip-dev libicu-dev && docker-php-ext-install pdo zip intl opcache

# Install PHP extensions
RUN docker-php-ext-install mbstring exif pcntl bcmath gd

# Support de MySQL
RUN docker-php-ext-install mysqli pdo_mysql

# Xdebug
RUN pecl install xdebug-2.8.1 && docker-php-ext-enable xdebug

# Xdebug copy to config
ADD xdebug.ini /usr/local/etc/php/conf.d/

# Install Composer.
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && ln -s $(composer config --global home) /root/composer


# Symfony tool
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony/bin/symfony /usr/local/bin/symfony

ENV PATH=$PATH:/root/composer/vendor/bin COMPOSER_ALLOW_SUPERUSER=1

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini

# Php.ini copy to config
ADD php.ini /etc/php/7.4/fpm/conf.d/php.ini

# Create system user
RUN groupadd -g 1000 gesco && useradd -u 1000 -g gesco -s /bin/bash gesco

# RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/gesco/.composer && \
    chown -R gesco:gesco /home/gesco


# Set working directory
WORKDIR /var/www/gesco

USER $user

EXPOSE 9000