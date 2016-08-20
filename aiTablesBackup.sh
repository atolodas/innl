#!/bin/sh
now="$(date +'%d_%m_%Y_%H_%M_%S')"
filename="tables_$now".gz
backupfolder="/var/www/html/var/backups"
fullpathbackupfile="$backupfolder/$filename"
logfile="$backupfolder/"backup_log_"$(date +'%Y_%m')".txt
echo "mysqldump started at $(date +'%d-%m-%Y %H:%M:%S')" >> "$logfile"
mysqldump --user=root --password=Minsk!11 --default-character-set=utf8 inl aiml aiml_userdefined | grep INSERT | sed 's/INSERT INTO/REPLACE INTO/' | gzip > "$fullpathbackupfile"
echo "mysqldump finished at $(date +'%d-%m-%Y %H:%M:%S')" >> "$logfile"
chown root "$fullpathbackupfile"
chown root "$logfile"
echo "file permission changed" >> "$logfile"
find "$backupfolder" -name tables_* -mtime +3 -exec rm {} \;
echo "old files deleted" >> "$logfile"
echo "operation finished at $(date +'%d-%m-%Y %H:%M:%S')" >> "$logfile"
echo "*****************" >> "$logfile"

exit 0
