Skid.KZ
================================

Skid.KZ - агрегатор данных со скидочных сервисов Республики Казахстан.

Адрес в стеи Интернет: http://skid.kz

Разработчики:
1. Петров Виталий, v31337@gmail.com
2. Абоимов Александр
3. Назмиев Руслан

Install
-------------------
0) ssh-keygen -t rsa and add this to bitbucked repo

1) Install composer

2) Clone bare repository 'cd /home/admin/; git clone --mirror git@bitbucket.org:kafeg/kupon.git' (manual: https://serverpilot.io/community/articles/how-to$<br/>

3) GIT_WORK_TREE=/home/admin/web/skid.kz/public_html/ git checkout -f master

4) cd /home/admin/web/skid.kz/public_html/

5) composer.phar global require "fxp/composer-asset-plugin:1.0.0"

6) composer.phar update

6.5) Setup database with config/db.php

7) php yii migrate

8) Setup apache virtualhost for /home/admin/web/skid.kz/public_html/web

9) Setup cron:

OLD:

 * */2 * * * /usr/bin/wget -O /dev/null -q http://skid.kz/index.php?r=kupon/default/fetchall\&pass=kafeg >/dev/null 2>&1

 * */5 * * * /usr/bin/wget -O /dev/null -q http://skid.kz/index.php?r=kupon/default/updateall\&pass=kafeg >/dev/null 2>&1

NEW:

 10 10 * * * /usr/bin/wget -O /dev/null -q http://skid.kz/kupon/default/fetchall

 30 2 * * * /usr/bin/wget -O /dev/null -q http://skid.kz/kupon/default/updateall

10) Setup mysql wait_timeout=360

11) Setup php max_execution_time to 240 or 360

12) sudo apt-get install php-apc

13) Setup clean urls for host in your apache or nginx.

Deploy
------------------
skid.kz/deploy/bitbucket-hook-skid.kz.php

Setup developing environment.
------------------
1. Install git, vagrant, composer
2. Git clone this project
3. "composer install" 
4. "vagrant up" command from project root
4. Wait for Ansible infrastructure provisioning
5. "vagrant ssh" start ssh session on virtual machine and connect
6. Project files will be mounted in /vagrant folder. Files are syncing automatically


Test services!
--------------------
http://skid.kz/kupon/default/testapi?serviceId=1
http://skid.kz/kupon/default/testapi?serviceId=2
http://skid.kz/kupon/default/testapi?serviceId=3
http://skid.kz/kupon/default/testapi?serviceId=4
http://skid.kz/kupon/default/testapi?serviceId=5
