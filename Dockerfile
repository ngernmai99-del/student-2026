FROM php:8.2-cli

# ຕິດຕັ້ງ extension zip ສຳລັບການສົ່ງອອກໄຟລ໌ Excel (.xlsx)
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . /app/

RUN mkdir -p /app/data/sessions \
    && chmod -R 777 /app/data

ENV PORT=8080
EXPOSE 8080

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t /app"]
