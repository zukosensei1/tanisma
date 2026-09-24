FROM php:8.2-apache

# gerekli eklentiler
RUN apt-get update && apt-get install -y \
    curl \
    && docker-php-ext-install curl

# dosyaları sunucuya kopyala
COPY . /var/www/html/

# Apache'yi başlat
EXPOSE 80
