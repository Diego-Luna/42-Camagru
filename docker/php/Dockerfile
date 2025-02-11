FROM php:8.2-fpm

# Permitir el paso del argumento MAIL_FROM
ARG MAIL_FROM

# Instalar dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    netcat-traditional \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    msmtp \
    msmtp-mta \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && docker-php-ext-enable pdo_mysql

# Configurar msmtp para redirigir correos a MailHog
RUN echo "defaults" > /etc/msmtprc \
    && echo "auth off" >> /etc/msmtprc \
    && echo "tls off" >> /etc/msmtprc \
    && echo "logfile /var/log/msmtp.log" >> /etc/msmtprc \
    && echo "account mailhog" >> /etc/msmtprc \
    && echo "host mailhog" >> /etc/msmtprc \
    && echo "port 1025" >> /etc/msmtprc \
    && echo "from ${MAIL_FROM}" >> /etc/msmtprc \
    && echo "account default: mailhog" >> /etc/msmtprc

# Configurar PHP para usar msmtp como sendmail
RUN echo "sendmail_path = /usr/bin/msmtp -t" > /usr/local/etc/php/conf.d/mail.ini

RUN echo "account default\nhost mailhog\nport 1025\nfrom camagru@example.com\nauto_from on\ntls off" > /etc/msmtprc \
    && echo "sendmail_path = /usr/bin/msmtp -t" > /usr/local/etc/php/conf.d/mail.ini

# Instalar Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


RUN mkdir -p /var/log/php && chmod 777 /var/log/php

RUN mkdir -p /var/www/camagru/src/public/controllers/uploads
RUN chown -R www-data:www-data /var/www/camagru/src/public/controllers/uploads
RUN chmod -R 755 /var/www/camagru/src/public/controllers/uploads

# Establecer el directorio de trabajo
WORKDIR /var/www/camagru
