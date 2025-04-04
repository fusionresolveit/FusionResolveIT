#!/bin/bash -e

ROOT_DIR=$(readlink -f "$(dirname $0)/../..")

echo "Check for syntax errors"
vendor/bin/parallel-lint \
  --exclude ./files/ \
  --exclude ./plugins/ \
  --exclude ./tools/vendor/ \
  --exclude ./vendor/ \
  .

echo "Check for missing dependencies / bad symbols"
# Alpine linux does not implement GLOB_BRACE.
# We have to define it to 0 to prevent "Warning: Use of undefined constant GLOB_BRACE - assumed 'GLOB_BRACE'" error.
# This is not a problem as long as we do not use braces in "scan-files" section of the config file.
php -d memory_limit=1G \
  -r 'define("GLOB_BRACE", 0); include "./vendor/maglnet/composer-require-checker/bin/composer-require-checker.php";' \
  check --config-file=.composer-require-checker.config.json

echo "Check for coding standards violations"
touch ~/phpcs.cache
vendor/bin/phpcs \
  --cache ~/phpcs.cache \
  -d memory_limit=512M \
  -p \
  --extensions=php \
  --ignore="/.git/,^$ROOT_DIR/(config|files|lib|node_modules|plugins|tests/config|vendor)/" \
  .
