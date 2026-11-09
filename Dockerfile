FROM php:8.2-apache

# ຕິດຕັ້ງ extension zip ສຳລັບການສົ່ງອອກໄຟລ໌ Excel (.xlsx)
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# ຕັ້ງຄ່າຄວາມປອດໄພຂອງ Apache (ປິດການສະແດງລາຍຊື່ໄຟລ໌ ແລະ ບລັອກໂຟນເດີ data / .sqlite ໂດຍກົງ)
RUN echo "ServerTokens Prod\nServerSignature Off\n\
<Directory /var/www/html>\n\
    Options -Indexes +FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n\
<Directory /var/www/html/data>\n\
    Require all denied\n\
</Directory>\n\
<FilesMatch \"\\.(sqlite|sqlite3|db|sql)$\">\n\
    Require all denied\n\
</FilesMatch>\n" > /etc/apache2/conf-available/security-custom.conf \
    && a2enconf security-custom

WORKDIR /var/www/html
COPY . /var/www/html/

# ກຳນົດສິດໃຫ້ໂຟນເດີ data ສາມາດຂຽນໄຟລ໌ຖານຂໍ້ມູນ SQLite ແລະ Session ໄດ້
RUN mkdir -p /var/www/html/data/sessions \
    && chown -R www-data:www-data /var/www/html/data \
    && chmod -R 770 /var/www/html/data

EXPOSE 80

# ຮອງຮັບ Dynamic PORT ຂອງ Railway Cloud
CMD ["sh", "-c", "sed -i \"s/80/${PORT:-80}/g\" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && apache2-foreground"]
