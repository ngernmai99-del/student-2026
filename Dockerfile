FROM php:8.2-apache

# ຕິດຕັ້ງ extension zip ສຳລັບການສົ່ງອອກໄຟລ໌ Excel (.xlsx)
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . /var/www/html/

# ສ້າງໂຟນເດີ ແລະ ກຳນົດສິດ
RUN mkdir -p /var/www/html/data/sessions \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 777 /var/www/html/data

ENV PORT=80
EXPOSE 80

# Script ເລີ່ມຕົ້ນ Apache ຮອງຮັບ Port ຂອງ Railway
CMD ["sh", "-c", "sed -i \"s/Listen 80/Listen ${PORT:-80}/\" /etc/apache2/ports.conf && sed -i \"s/:80/:${PORT:-80}/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
