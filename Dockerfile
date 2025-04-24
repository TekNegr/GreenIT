FROM php:8.2-apache

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git zip unzip libzip-dev && docker-php-ext-install zip


# Copier les fichiers dans le conteneur
COPY . /var/www/html

LABEL maintainer="ton.nom@email.com" \
      version="0.1" \
      description="GreenIT - Analyse de l'impact écologique de projets logiciels en Python"

# Config Laravel
WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php && \
    php composer.phar install

CMD ["apache2-foreground"]

# test
