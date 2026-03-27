# Use the official PHP 8.4 with Apache image
FROM php:8.4-apache

# Set non-interactive installations
ENV DEBIAN_FRONTEND=noninteractive

# Update packages and install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql pgsql bcmath gd zip intl

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Change Apache document root to Laravel public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js and NPM for Vite build
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies (ignoring system requirements for speed, assuming extensions are there)
RUN composer install --no-dev --optimize-autoloader

# Install NPM dependencies and build static assets
RUN npm install
RUN npm run build

# Set permissions for the web user
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Copy start script and set permissions
COPY start.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/start.sh

# Expose port (Render sets PORT env)
EXPOSE 80

# Use start script
CMD ["start.sh"]
