# Use the official FrankenPHP image
FROM dunglas/frankenphp:latest-php8.2

# 1. Install system tools (FIX FOR YOUR ERROR)
# We add git and unzip so Composer doesn't fail when downloading packages
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Install PHP extensions required for Laravel
RUN install-php-extensions \
    pcntl \
    bcmath \
    gd \
    intl \
    zip \
    opcache \
    pdo_mysql \
    redis

# 3. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Set working directory
WORKDIR /app

# 5. Copy your custom php settings
COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# 6. Start Octane
CMD ["php", "artisan", "octane:start", "--server=frankenphp", "--host=0.0.0.0", "--port=8000", "--workers=auto"]