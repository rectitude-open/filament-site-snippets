#!/bin/sh
set -e
cd /home/wwwroot/filament-site-snippets || exit
chown -R www-data:www-data /home/wwwroot/filament-site-snippets && \
find /home/wwwroot/filament-site-snippets -type f -exec chmod 644 {} \; && \
find /home/wwwroot/filament-site-snippets -type d -exec chmod 755 {} \; && \
chmod -R +x /home/wwwroot/filament-site-snippets/vendor/bin/ && \
chmod -R +x /home/wwwroot/filament-site-snippets/dev/
