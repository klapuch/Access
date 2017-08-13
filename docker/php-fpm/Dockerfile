FROM php:rc-fpm

# PHP
RUN apt-get update -qq && apt-get install -y git sudo libcurl4-openssl-dev gnupg \
	&& apt-get install -y libpq-dev && docker-php-ext-install pdo_pgsql \
	&& apt-get install -y zlib1g-dev zip unzip && docker-php-ext-install zip

# COMPOSER
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# CLEANING
RUN apt-get clean