FROM trafex/alpine-nginx-php7
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY . /var/www/html/chintomi

WORKDIR /var/www/html/chintomi
USER root
RUN apk update && apk add php7-fileinfo
RUN composer remove --dev phpunit/phpunit && composer install --no-dev
RUN chown -R nobody vendor 

WORKDIR / 
RUN mkdir -p chintomi/library && mkdir -p chintomi/books && chown -R nobody chintomi
USER nobody
