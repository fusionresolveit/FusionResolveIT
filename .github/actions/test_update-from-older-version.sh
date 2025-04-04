#!/bin/bash -e

LOG_FILE="./tests/files/_log/migration.log"
mkdir -p $(dirname "$LOG_FILE")

# Reconfigure DB
echo "reconfigure DB"
bin/console glpi:database:configure \
  --config-dir=./tests/config --ansi --no-interaction \
  --reconfigure --db-name=glpitest0723 --db-host=127.0.0.1 --db-user=gsit --db-password=gsitpassword

# Execute update
echo "Execute update"
## First run should do the migration.
echo "First run should do the migration"
bin/console glpi:database:update --config-dir=./tests/config --ansi --no-interaction --allow-unstable | tee $LOG_FILE
if [[ -n $(grep "No migration needed." $LOG_FILE) ]];
  then echo "bin/console glpi:database:update command FAILED" && exit 1;
fi
## Second run should do nothing.
echo "Second run should do nothing"
bin/console glpi:database:update --config-dir=./tests/config --ansi --no-interaction --allow-unstable | tee $LOG_FILE
if [[ -z $(grep "No migration needed." $LOG_FILE) ]];
  then echo "bin/console glpi:database:update command FAILED" && exit 1;
fi

# Execute myisam_to_innodb migration
echo "Execute myisam_to_innodb migration"
## First run should do the migration.
echo "First run should do the migration"
bin/console glpi:migration:myisam_to_innodb --config-dir=./tests/config --ansi --no-interaction | tee $LOG_FILE
if [[ -n $(grep "No migration needed." $LOG_FILE) ]];
  then echo "bin/console glpi:migration:myisam_to_innodb command FAILED" && exit 1;
fi
## Second run should do nothing.
echo "Second run should do nothing"
bin/console glpi:migration:myisam_to_innodb --config-dir=./tests/config --ansi --no-interaction | tee $LOG_FILE
if [[ -z $(grep "No migration needed." $LOG_FILE) ]];
  then echo "bin/console glpi:migration:myisam_to_innodb command FAILED" && exit 1;
fi

# Execute timestamps migration
echo "Execute timestamps migration"
## First run should do the migration.
echo "First run should do the migration"
bin/console glpi:migration:timestamps --config-dir=./tests/config --ansi --no-interaction | tee $LOG_FILE
if [[ -n $(grep "No migration needed." $LOG_FILE) ]];
  then echo "bin/console glpi:migration:timestamps command FAILED" && exit 1;
fi
## Second run should do nothing.
echo "Second run should do nothing"
bin/console glpi:migration:timestamps --config-dir=./tests/config --ansi --no-interaction | tee $LOG_FILE
if [[ -z $(grep "No migration needed." $LOG_FILE) ]];
  then echo "bin/console glpi:migration:timestamps command FAILED" && exit 1;
fi

# Test that updated DB has same schema as newly installed DB
echo "reconfigure database"
bin/console glpi:database:configure \
  --config-dir=./tests/config --no-interaction \
  --reconfigure --db-host=127.0.0.1 --db-name=gsit --db-user=gsit --db-password=gsitpassword
echo "run Atoum to test DB"
vendor/bin/atoum \
  -p 'php -d memory_limit=512M' \
  --debug \
  --force-terminal \
  --use-dot-report \
  --bootstrap-file tests/bootstrap.php \
  --no-code-coverage \
  --fail-if-skipped-methods \
  --max-children-number 1 \
  -d tests/database
