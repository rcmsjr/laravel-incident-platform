ARG PHP_VERSION=8.3
FROM laradock/workspace:latest-${PHP_VERSION}

# Set Environment Variables
ENV DEBIAN_FRONTEND noninteractive

# Start as root
USER root

###########################################################################
# Laradock non-root user:
###########################################################################

# Add a non-root user to prevent files being created with root permissions on host machine.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

# always run apt update when start and after add new source list, then clean up at end.
RUN set -xe; \
  apt-get update -yqq && \
  pecl channel-update pecl.php.net && \
  if [ $(php -r "echo PHP_MAJOR_VERSION;") = "8" ] && [ $(php -r "echo PHP_MINOR_VERSION;") != "0" ]; then \
  groupmod --new-name laradock ubuntu; \
  usermod --login laradock ubuntu --move-home --home /home/laradock; \
  else \
  groupadd -g ${PGID} laradock; \
  useradd -l -u ${PUID} -g laradock -m laradock -G docker_env; \
  fi; \
  usermod -p "*" laradock -s /bin/bash && \
  apt-get install -yqq \
  apt-utils \
  #
  #--------------------------------------------------------------------------
  # Mandatory Software's Installation
  #--------------------------------------------------------------------------
  #
  # Mandatory Software's such as ("php-cli", "git", "vim", ....) are
  # installed on the base image 'laradock/workspace' image. If you want
  # to add more Software's or remove existing one, you need to edit the
  # base image (https://github.com/Laradock/workspace).
  #
  # next lines are here because there is no auto build on dockerhub see https://github.com/laradock/laradock/pull/1903#issuecomment-463142846
  libzip-dev zip unzip \
  # Install the zip extension
  php${PHP_VERSION}-zip \
  # nasm
  nasm && \
  php -m | grep -q 'zip'

###########################################################################
# Set Timezone
###########################################################################

ARG TZ=UTC
ENV TZ ${TZ}

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

###########################################################################
# User Aliases
###########################################################################

USER root

COPY ./aliases.sh /root/aliases.sh
COPY ./aliases.sh /home/laradock/aliases.sh

RUN sed -i 's/\r//' /root/aliases.sh && \
  sed -i 's/\r//' /home/laradock/aliases.sh && \
  chown laradock:laradock /home/laradock/aliases.sh && \
  echo "" >> ~/.bashrc && \
  echo "# Load Custom Aliases" >> ~/.bashrc && \
  echo "source ~/aliases.sh" >> ~/.bashrc && \
  echo "" >> ~/.bashrc

USER laradock

RUN echo "" >> ~/.bashrc && \
  echo "# Load Custom Aliases" >> ~/.bashrc && \
  echo "source ~/aliases.sh" >> ~/.bashrc && \
  echo "" >> ~/.bashrc

###########################################################################
# Composer:
###########################################################################

USER root

# Add the composer.json
COPY ./composer.json /home/laradock/.composer/composer.json

# Make sure that ~/.composer belongs to laradock
RUN chown -R laradock:laradock /home/laradock/.composer

# Export composer vendor path
RUN echo "" >> ~/.bashrc && \
  echo 'export PATH="$HOME/.composer/vendor/bin:$PATH"' >> ~/.bashrc

# Update composer
ARG COMPOSER_VERSION=2
ENV COMPOSER_VERSION ${COMPOSER_VERSION}
RUN set -eux; \
  if [ "$COMPOSER_VERSION" = "1" ] || [ "$COMPOSER_VERSION" = "2" ] || [ "$COMPOSER_VERSION" = "2.2" ]; then \
  composer self-update --${COMPOSER_VERSION}; \
  else \
  composer self-update ${COMPOSER_VERSION}; \
  fi

USER laradock

# Install composer globally
RUN composer global install

# Export composer vendor path
RUN echo "" >> ~/.bashrc && \
  echo 'export PATH="~/.composer/vendor/bin:$PATH"' >> ~/.bashrc

###########################################################################
# Non-root user : PHPUnit path
###########################################################################

# add ./vendor/bin to non-root user's bashrc (needed for phpunit)
USER laradock

RUN echo "" >> ~/.bashrc && \
  echo 'export PATH="/var/www/vendor/bin:$PATH"' >> ~/.bashrc

###########################################################################
# PHP REDIS EXTENSION
###########################################################################

USER root

RUN apt-get update \
  && apt-get install -yqq php${PHP_VERSION}-redis

###########################################################################
# Node / NVM:
###########################################################################

USER laradock

# Check if NVM needs to be installed
ARG NODE_VERSION=node
ENV NODE_VERSION ${NODE_VERSION}
ARG INSTALL_NPM_VUE_CLI=false
ARG INSTALL_NPM_CHECK_UPDATES_CLI=false
ENV NVM_DIR /home/laradock/.nvm

# Install nvm (A Node Version Manager)
RUN mkdir -p $NVM_DIR && \
  curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/install.sh | bash \
  && . $NVM_DIR/nvm.sh \
  && nvm install ${NODE_VERSION} \
  && nvm use ${NODE_VERSION} \
  && nvm alias ${NODE_VERSION} \
  && npm cache clear --force \
  && npm install -g @vue/cli \
  && npm install -g npm-check-updates

# Wouldn't execute when added to the RUN statement in the above block
# Source NVM when loading bash since ~/.profile isn't loaded on non-login shell
RUN echo "" >> ~/.bashrc && \
  echo 'export NVM_DIR="$HOME/.nvm"' >> ~/.bashrc && \
  echo '[ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh"  # This loads nvm' >> ~/.bashrc

# Add NVM binaries to root's .bashrc
USER root

RUN echo "" >> ~/.bashrc && \
  echo 'export NVM_DIR="/home/laradock/.nvm"' >> ~/.bashrc && \
  echo '[ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh"  # This loads nvm' >> ~/.bashrc

# Make it so the node modules can be executed with 'docker-compose exec'
# We'll create symbolic links into '/usr/local/bin'.
RUN find $NVM_DIR -type f -name node -exec ln -s {} /usr/local/bin/node \; && \
  NODE_MODS_DIR="$NVM_DIR/versions/node/$(node -v)/lib/node_modules" && \
  ln -s $NODE_MODS_DIR/npm/bin/npm-cli.js /usr/local/bin/npm && \
  ln -s $NODE_MODS_DIR/npm/bin/npx-cli.js /usr/local/bin/npx && \
  ln -s $NODE_MODS_DIR/vue-cli/bin/vue /usr/local/bin/vue && \
  ln -s $NODE_MODS_DIR/vue-cli/bin/vue-init /usr/local/bin/vue-init && \
  ln -s $NODE_MODS_DIR/vue-cli/bin/vue-list /usr/local/bin/vue-list

# Mount .npmrc into home folder
COPY ./.npmrc /root/.npmrc
COPY ./.npmrc /home/laradock/.npmrc

###########################################################################
# PNPM:
###########################################################################

USER root

ENV PNPM_HOME="/home/laradock/.local/share/pnpm"
ENV PATH $PATH:/home/laradock/.local/share/pnpm

RUN echo "" >> ~/.bashrc && \
  echo 'export PNPM_HOME="/home/laradock/.local/share/pnpm"' >> ~/.bashrc && \
  echo 'export PATH="$PNPM_HOME:$PATH"' >> ~/.bashrc && \
  npm install -g pnpm

###########################################################################
# YARN:
###########################################################################

USER laradock

ARG YARN_VERSION=latest
ENV YARN_VERSION ${YARN_VERSION}

RUN [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh" && \
  if [ ${YARN_VERSION} = "latest" ]; then \
  curl -o- -L https://yarnpkg.com/install.sh | bash; \
  else \
  curl -o- -L https://yarnpkg.com/install.sh | bash -s -- --version ${YARN_VERSION}; \
  fi && \
  echo "" >> ~/.bashrc && \
  echo 'export PATH="$HOME/.yarn/bin:$PATH"' >> ~/.bashrc

# Add YARN binaries to root's .bashrc
USER root

RUN echo "" >> ~/.bashrc && \
  echo 'export YARN_DIR="/home/laradock/.yarn"' >> ~/.bashrc && \
  echo 'export PATH="$YARN_DIR/bin:$PATH"' >> ~/.bashrc

# Add PATH for YARN
ENV PATH $PATH:/home/laradock/.yarn/bin

###########################################################################
# Minio:
###########################################################################

USER root

COPY mc/config.json /root/.mc/config.json

RUN curl -fsSL -o /usr/local/bin/mc https://dl.minio.io/client/mc/release/linux-amd64/mc && \
  chmod +x /usr/local/bin/mc

###########################################################################
# Check PHP version:
###########################################################################

RUN set -xe; php -v | head -n 1 | grep -q "PHP ${PHP_VERSION}."

#
#--------------------------------------------------------------------------
# Final Touch
#--------------------------------------------------------------------------
#

USER root

# Clean up
RUN apt-get clean && \
  rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
  rm -f /var/log/lastlog /var/log/faillog

# Set default work directory
WORKDIR /var/www
