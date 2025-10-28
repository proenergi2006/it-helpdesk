GNU nano 6.2                                                              start.sh
#!/bin/sh

# Start PHP-FPM in background
php-fpm -D

# Start nginx in foreground
nginx -g "daemon off;"
