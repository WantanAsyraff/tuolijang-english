FROM m.daocloud.io/docker.io/library/php:8.0-cli

# 设置时区
ENV TZ=Asia/Shanghai
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# 替换为阿里云源
RUN sed -i 's/deb.debian.org/mirrors.aliyun.com/g' /etc/apt/sources.list \
    && sed -i 's/security.debian.org/mirrors.aliyun.com/g' /etc/apt/sources.list

# 安装系统依赖
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libssl-dev \
    libmcrypt-dev \
    libmagickwand-dev \
    unzip \
    zip \
    vim \
    supervisor \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install zip \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install exif \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install sockets \
    && docker-php-ext-install gettext \
    && rm -rf /var/lib/apt/lists/*

# 安装Swoole扩展 (指定兼容PHP 8.0的版本)
RUN pecl install swoole-4.8.12 \
    && docker-php-ext-enable swoole

# 安装Redis扩展
RUN pecl install redis \
    && docker-php-ext-enable redis

# 安装APCu扩展（替代apcu_store）
RUN pecl install apcu \
    && docker-php-ext-enable apcu

# 复制所有swoole_loader扩展到临时目录供entrypoint.sh使用
COPY ./docker/php/extensions/ /tmp/extensions/

# 创建基本的swoole_loader配置文件（会被entrypoint.sh覆盖）
RUN echo ";extension=swoole_loader80.so" > /usr/local/etc/php/conf.d/swoole_loader.ini

# 安装Composer
# COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_PROCESS_TIMEOUT=1200
ENV COMPOSER_HTTP_TIMEOUT=1200

# 设置工作目录
WORKDIR /var/www

# 复制PHP配置
COPY ./docker/php/config/php.ini /usr/local/etc/php/conf.d/custom.ini

# 创建必要的目录
RUN mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/bootstrap/cache \
    && chmod -R 777 /var/www/storage \
    && chmod -R 777 /var/www/bootstrap/cache

# 复制入口脚本
COPY ./docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# 暴露端口
EXPOSE 20200

# 设置入口点
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php", "./bin/laravels", "start", "--ignore"]
