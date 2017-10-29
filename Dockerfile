FROM php:7-apache

LABEL Name=pdf-converter Version=0.0.1

RUN apt-get update \
	&& apt-get install -y --no-install-recommends \
		libreoffice-writer \
	&& a2enmod rewrite ssl \
	&& apt-get autoremove -y \
	&& rm -r /var/lib/apt/lists/*

COPY scripts/assets/php.ini /usr/local/etc/php/php.ini
COPY scripts/assets/site.conf /etc/apache2/sites-available/000-default.conf

COPY .htaccess /srv/pdf-converter/
COPY bootstrap.php /srv/pdf-converter/
COPY src /srv/pdf-converter/src/
COPY vendor /srv/pdf-converter/vendor/
