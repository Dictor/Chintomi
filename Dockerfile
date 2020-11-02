FROM trafex/alpine-nginx-php7
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY . /var/www/html

WORKDIR /var/www/html
USER root
RUN apk update && apk add php7-fileinfo php7-dom php7-xmlwriter
RUN composer install --no-dev
RUN chown -R nobody vendor 

WORKDIR / 
RUN mkdir -p chintomi/library && mkdir -p chintomi/books && chown -R nobody chintomi
USER nobody
