# Credits: https://github.com/dunglas/symfony-docker/blob/master/Dockerfile

# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG PHP_VERSION=8.0
ARG APACHE_VERSION=2.4
ARG NODE_VERSION=14

###############################################
# "node" stage
FROM node:${NODE_VERSION} AS idea_node

WORKDIR /srv/app

COPY package.json yarn.lock webpack.config.js ./

RUN set -eux; \
    yarn install;

COPY assets assets/

RUN set -eux; \
    yarn run build --mode production

###############################################
# "php" stage
FROM php:${PHP_VERSION}-fpm-alpine AS idea_php

# persistent / runtime deps
RUN apk add --no-cache \
	acl \
	fcgi \
	file \
	gettext \
	git \
	jq \
	;

ARG APCU_VERSION=5.1.18
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
	$PHPIZE_DEPS \
	icu-dev \
	libjpeg-turbo-dev \
	libpng-dev \
	libzip-dev \
	zlib-dev \
	libxslt-dev \
	; \
	\
	docker-php-ext-configure gd  --with-jpeg; \
	docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
	gd \
	intl \
	pdo_mysql \
	xsl \
	zip \
	; \
	pecl install \
	apcu-${APCU_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
	apcu \
	opcache \
	; \
	\
	runDeps="$( \
	scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
	| tr ',' '\n' \
	| sort -u \
	| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/conf.d/symfony.ini $PHP_INI_DIR/conf.d/symfony.ini

RUN set -eux; \
	{ \
	echo '[www]'; \
	echo 'ping.path = /ping'; \
	} | tee /usr/local/etc/php-fpm.d/docker-healthcheck.conf

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
# install Symfony Flex globally to speed up download of Composer packages (parallelized prefetching)
RUN set -eux; \
	composer global require "symfony/flex" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/app

# build for production
ENV APP_ENV=prod
ENV ROUTER_REQUEST_CONTEXT_HOST=ideas.aulasoftwarelibre.uco.es

COPY composer.json composer.lock symfony.lock ./
RUN set -eux; \
	composer install --no-dev --prefer-dist --no-progress --no-scripts --no-interaction; \
	composer clear-cache

COPY .env ./
COPY bin bin/
COPY config config/
COPY migrations migrations/
COPY public public/
COPY assets assets/
COPY --from=idea_node /srv/app/public/build public/build/
COPY templates templates/
COPY translations translations/
COPY src src/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-scripts --no-dev --optimize; \
	composer run-script post-install-cmd --no-dev; sync

VOLUME ["/srv/app/var", "/srv/app/public/cache", "/srv/app/public/images"]

# healthcheck
COPY docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck
HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

# entrypoint
COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

###############################################
# "apache" stage
FROM httpd:${APACHE_VERSION} AS idea_httpd

EXPOSE 80

RUN apt-get update; \
	apt-get install -y liblasso3 curl; \
	apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*; \
	mkdir -p /srv/app/public/cache /srv/app/public/images

COPY docker/httpd/conf.d/httpd.conf /usr/local/apache2/conf/httpd.conf

WORKDIR /srv/app

COPY --from=idea_php /srv/app/public public/
