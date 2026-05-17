ARG PHP_VERSION=8.3
FROM laradock/php-fpm:latest-${PHP_VERSION}

# Redeclare ARG, which is required if you want to use it in later RUN steps after the FROM instruction.
ARG PHP_VERSION=8.3

# Set Environment Variables
ENV DEBIAN_FRONTEND noninteractive

# always run apt update when start and after add new source list, then clean up at end.
RUN set -xe; \
  apt-get update -yqq && \
  pecl channel-update pecl.php.net && \
  apt-get install -yqq \
  apt-utils \
  gnupg2 \
  git \
  #
  #--------------------------------------------------------------------------
  # Mandatory Software's Installation
  #--------------------------------------------------------------------------
  #
  # Mandatory Software's such as ("mcrypt", "pdo_mysql", "libssl-dev", ....)
  # are installed on the base image 'laradock/php-fpm' image. If you want
  # to add more Software's or remove existing one, you need to edit the
  # base image (https://github.com/Laradock/php-fpm).
  #
  # next lines are here becase there is no auto build on dockerhub see https://github.com/laradock/laradock/pull/1903#issuecomment-463142846
  libzip-dev zip unzip && \
  if [ ${PHP_VERSION} = "7.3" ] || [ ${PHP_VERSION} = "7.4" ] || [ $(php -r "echo PHP_MAJOR_VERSION;") = "8" ]; then \
  docker-php-ext-configure zip; \
  else \
  docker-php-ext-configure zip --with-libzip; \
  fi && \
  # Install the zip extension
  docker-php-ext-install zip && \
  php -m | grep -q 'zip'

###########################################################################
# bcmath:
###########################################################################

# Install the bcmath extension
RUN docker-php-ext-install bcmath

###########################################################################
# Mysqli Modifications:
###########################################################################

RUN docker-php-ext-install mysqli

###########################################################################
# pgsql
###########################################################################

# Install the pgsql extension
RUN docker-php-ext-install pgsql

###########################################################################
# PHP REDIS EXTENSION
###########################################################################

# Install Php Redis Extension
RUN if [ $(php -r "echo PHP_MAJOR_VERSION;") = "5" ]; then \
  pecl install -o -f redis-4.3.0; \
  elif [ $(php -r "echo PHP_MAJOR_VERSION;") = "7" ] && { [ $(php -r "echo PHP_MINOR_VERSION;") = "0" ] || [ $(php -r "echo PHP_MINOR_VERSION;") = "1" ] ;}; then \
  pecl install -o -f redis-5.3.7; \
  elif [ $(php -r "echo PHP_MAJOR_VERSION;") = "7" ] && { [ $(php -r "echo PHP_MINOR_VERSION;") = "2" ] || [ $(php -r "echo PHP_MINOR_VERSION;") = "3" ] ;}; then \
  pecl install -o -f redis-6.0.2; \
  else \
  pecl install -o -f redis; \
  fi \
  && rm -rf /tmp/pear \
  && docker-php-ext-enable redis

###########################################################################
# Image optimizers:
###########################################################################

USER root

RUN apt-get install -yqq jpegoptim optipng pngquant gifsicle

###########################################################################
# Human Language and Character Encoding Support:
###########################################################################

# Install intl and requirements
RUN apt-get install -yqq zlib1g-dev libicu-dev g++ && \
  docker-php-ext-configure intl && \
  docker-php-ext-install intl

###########################################################################
# DNS utilities:
###########################################################################

USER root

RUN apt-get update && apt-get install -y dnsutils

###########################################################################
# Check PHP version:
###########################################################################

RUN set -xe; php -v | grep -q "PHP ${PHP_VERSION}."

#
#--------------------------------------------------------------------------
# Final Touch
#--------------------------------------------------------------------------
#

COPY ./laravel.ini /usr/local/etc/php/conf.d
COPY ./xlaravel.pool.conf /usr/local/etc/php-fpm.d/

USER root

# Clean up
RUN apt-get clean && \
  rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
  rm -f /var/log/lastlog /var/log/faillog

# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN groupmod -o -g ${PGID} www-data && \
  usermod -o -u ${PUID} -g www-data www-data

# Configure locale.
ARG LOCALE=POSIX
ENV LC_ALL ${LOCALE}

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
