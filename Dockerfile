# استخدم نسخة PHP رسمية
FROM php:8.2-cli

# تثبيت system dependencies وامتدادات PHP المطلوبة
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl gd

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إعداد مجلد العمل
WORKDIR /app

# نسخ كل الملفات
COPY . .

# تثبيت الباكججات
RUN composer install --no-dev --optimize-autoloader

# إعداد الـ APP_KEY في مرحلة البناء (اختياري – ممكن تخليه في .env على Railway)
# RUN php artisan key:generate

# فتح البورت
EXPOSE 8000

# أمر التشغيل
CMD php artisan serve --host=0.0.0.0 --port=8000
