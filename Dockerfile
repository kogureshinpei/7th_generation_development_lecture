FROM php:8.3-apache

# MySQL に PDO で接続するための拡張を有効化
RUN docker-php-ext-install pdo_mysql

# 日本時間で日時を扱う
RUN echo "date.timezone=Asia/Tokyo" > /usr/local/etc/php/conf.d/timezone.ini
