#!/bin/sh
now="$(date +%s)"
filename="$now"_db_cron.gz
backupfolder="/var/www/html/var/backups"
fullpathbackupfile="$backupfolder/$filename"
logfile="$backupfolder/"backup_log_"$(date +'%Y_%m')".txt
echo "mysqldump started at $(date +'%d-%m-%Y %H:%M:%S')" >> "$logfile"
mysqldump --user=root --default-character-set=utf8 --password=Minsk!11 inl | gzip > "$fullpathbackupfile"
echo "mysqldump finished at $(date +'%d-%m-%Y %H:%M:%S')" >> "$logfile"
chown root "$fullpathbackupfile"
chown root "$logfile"
echo "file permission changed" >> "$logfile"
find "$backupfolder" -name *_db_cron.gz -mtime +3 -exec rm {} \;
echo "old files deleted" >> "$logfile"
echo "operation finished at $(date +'%d-%m-%Y %H:%M:%S')" >> "$logfile"
echo "*****************" >> "$logfile"

exit 0
