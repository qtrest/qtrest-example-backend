Skid.KZ
================================

Skid.KZ - агрегатор данных со скидочных сервисов Республики Казахстан.

Install
-------------------
1) Install composer
2) Clone bare repository 'cd /home/admin/; git clone --mirror git@bitbucket.org:kafeg/kupon.git' (manual: https://serverpilot.io/community/articles/how-to$
3) cd /home/admin/web/skid.kz/public_html/
4) GIT_WORK_TREE=/home/admin/web/skid.kz/public_html/ git checkout -f master
5) composer.phar global require "fxp/composer-asset-plugin:1.0.0"
6) composer.phar update
7) php yii migrate
8) Setup apache virtualhost
9) Setup cron:
* */2 * * * /usr/bin/wget -O - http://skid.kz/index.php?r=/fetchall\&pass=kafeg >/dev/null 2>&1
* */3 * * * /usr/bin/wget -O - http://skid.kz/index.php?r=/updateall\&pass=kafeg >/dev/null 2>&1
10) Setup mysql wait_timeout=360
11) Setup php max_execution_time to 240 or 360
12) sudo apt-get install php-apc

Deploy
------------------
skid.kz/deploy/bitbucket-hook-skid.kz.php
