#!/bin/bash
CRON_JOB="*/5 * * * * /usr/bin/php $(pwd)/cron.php"
(crontab -l 2>/dev/null | grep -v -F "$CRON_JOB" ; echo "$CRON_JOB") | crontab -
echo "Cron job set to run every 5 minutes."