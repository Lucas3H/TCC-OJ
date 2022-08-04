FROM php:7.4-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    npm 


# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*


RUN curl -sL https://deb.nodesource.com/setup_14.x 
RUN apt-get install nodejs

# Install PHP extensions
RUN docker-php-ext-install intl pdo pdo_mysql sockets zip pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /app

CMD ["composer", "update"]
CMD ["npm", "ci"]
CMD ["npm", "run", "dev"]


CMD ["php", "artisan", "serve", "--host=0.0.0.0" ,"--port=3000"]

USER $user
