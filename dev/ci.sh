#!/bin/bash
set -e

/home/wwwroot/filament-site-snippets/vendor/bin/pest
/home/wwwroot/filament-site-snippets/vendor/bin/pint
/home/wwwroot/filament-site-snippets/vendor/bin/phpstan analyse
