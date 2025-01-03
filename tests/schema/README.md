MySQL export used by phpstan

To regenerate this, do:

```
mysqldump -u user -p --no-data databasename > tests/schema/mysql-schema.dump
```
