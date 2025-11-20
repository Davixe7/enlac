/usr/bin/mysqldump
    --no-tablespaces \
    --no-create-info \
    --extended-insert \
    --single-transaction \
    --skip-lock-tables \
    --complete-insert \
    --ignore-table=pete75ru_enlac.cache \
    --ignore-table=pete75ru_enlac.cache_locks \
    --ignore-table=pete75ru_enlac.failed_jobs \
    --ignore-table=pete75ru_enlac.migrations \
    --ignore-table=pete75ru_enlac.password_reset_tokens \
    --ignore-table=pete75ru_enlac.personal_access_tokens \
    --ignore-table=pete75ru_enlac.sessions \
    pete75ru_enlac \
    > /home/smith/Proyectos/enlac/backups/backup_data_$(date +\%Y\%m\%d\%H\%M\%S).sql
