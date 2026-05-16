FROM php:8.2-cli

RUN apt-get update && apt-get install -y\
unzip\
git\
curl\
sqlite3\
libsqlite3-dev\
libzip-dev\
libpng-dev\
libonig-dev\
libxml2-dev\
zip\
nodejs\
npm\
&& docker-php-ext-install pdo pdo_sqlite zip mbstring exif pcntl bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

RUN npm install
RUN npm run build

RUN touch database/database.sqlite

EXPOSE 10000

CMD php artisan migrate --force && php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear && php artisan serve --host=0.0.0.0 --port=10000
