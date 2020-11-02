FROM trafex/alpine-nginx-php7
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY . /var/www/html

WORKDIR /var/www/html
USER root
RUN apk update && apk add php7-fileinfo php7-xml php7-xmlwriter php7-tokenizer
RUN composer install --no-dev
RUN chown -R nobody vendor 

WORKDIR / 
RUN mkdir -p chintomi/library && mkdir -p chintomi/books && chown -R nobody chintomi
USER nobody
