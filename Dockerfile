FROM php:8.2-apache

# Gerekli sistem paketleri (curl zaten imajda var, ekstra kurmaya gerek yok)
RUN apt-get update && apt-get install -y libcurl4-openssl-dev \
    && rm -rf /var/lib/apt/lists/*

# Dosyaları sunucuya kopyala
COPY . /var/www/html/

# Apache'yi başlat
EXPOSE 80
