ARG PHP_IMAGE_VERSION
FROM php:${PHP_IMAGE_VERSION}

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH ${PATH}:/composer/vendor/bin
ENV LANG C.UTF-8
ENV LANGUAGE en_US:

ARG XDEBUG_VERSION
ARG NODE_VERSION

RUN apt-get update \
  && curl -sL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
  && apt-get install -y libzip-dev zlib1g-dev unzip vim zip libpq-dev libonig-dev \
  libpng-dev libjpeg-dev libicu-dev nodejs \
  && npm install -g npm \
  && pecl install xdebug${XDEBUG_VERSION} apcu \
  && echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apcu.ini \
  && docker-php-ext-enable xdebug \
  && docker-php-ext-configure gd --with-jpeg \
  && docker-php-ext-install gd zip pdo_mysql bcmath intl mbstring opcache \
  && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php -r "if (hash_file('sha384', 'composer-setup.php') === trim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/local/bin/composer \
  && composer global require hirak/prestissimo \
  && composer global require laravel/installer \
  && curl -fsSL https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add - \
  && echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google-chrome.list \
  && apt update && apt install -y google-chrome-stable \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* google-chrome-stable_current_amd64.deb

# port
EXPOSE 22 80 443
