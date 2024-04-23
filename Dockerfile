FROM php:8.2-apache
COPY . /usr/src/myapp
WORKDIR /usr/src/myapp

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

EXPOSE 80

CMD ["apache2-foreground"]
