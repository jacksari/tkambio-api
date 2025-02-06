# Usar la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Habilitar módulos de Apache necesarios para Laravel
RUN a2enmod rewrite headers

# Instalar las extensiones necesarias para Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring gd bcmath zip opcache

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /var/www/html

# Cambiar el DocumentRoot de Apache para que apunte a la carpeta public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf


# Copiar los archivos de la aplicación al contenedor
COPY . /var/www/html

# Ajustar permisos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

RUN chmod -R 777 /var/www/html/storage \
    && chmod -R 777 /var/www/html/bootstrap/cache

#     chmod -R 777 /var/www/html/storage
# chmod -R 777 /var/www/html/bootstrap/cache



RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf


# Exponer el puerto 80
EXPOSE 80

# Ejecutar Composer install para instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Crear el volumen para mantener los datos persistentes
VOLUME ["/var/www/html"]

# Iniciar el servidor Apache
CMD ["apache2-foreground"]

# docker exec -it back-tkambio /bin/bash
# docker-compose down
# docker-compose up --build