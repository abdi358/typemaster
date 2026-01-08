#!/bin/bash
set -e

# Use PORT environment variable or default to 80
PORT=${PORT:-80}

echo "Configuring Apache to listen on port $PORT..."

# Replace "Listen 80" with "Listen $PORT" in ports.conf
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf

# Replace "<VirtualHost *:80>" with "<VirtualHost *:$PORT>" in default site config
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# Enable rewrite module (just in case)
a2enmod rewrite

# Start Apache in foreground
echo "Starting Apache..."
exec apache2-foreground
