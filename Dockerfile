FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
  curl \
  libcurl4-openssl-dev \
  libonig-dev \
  unzip \
  && docker-php-ext-install pdo_mysql curl \
  && a2enmod rewrite headers \
  && sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/000-default.conf \
  && sed -ri "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . /var/www/html

RUN mkdir -p /var/www/html/public/uploads/products \
  /var/www/html/public/uploads/news \
  /var/www/html/public/uploads/avatars \
  && chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]