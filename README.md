Skid.KZ
================================

Skid.KZ - агрегатор данных со скидочных сервисов Республики Казахстан.

Production URL: http://skid.kz

Developers:
1. Petrov Vitaliy, v31337@gmail.com
2. Aboimov Alexander

Install guide
-------------------
0) ssh-keygen -t rsa and add this to bitbucked repo

1) Install composer

2) Clone bare repository 'cd /home/admin/; git clone --mirror git@bitbucket.org:kafeg/kupon.git' (manual: https://serverpilot.io/community/articles/how-to$<br/>

3) GIT_WORK_TREE=/home/admin/web/skid.kz/public_html/ git checkout -f master

4) cd /home/admin/web/skid.kz/public_html/

5) composer.phar global require "fxp/composer-asset-plugin:~1.0.3"

6) composer.phar update

6.5) Setup database with config/db.php

7) php yii migrate

8) Setup apache virtualhost for web and api parts. See example in docs/skid.kz.apacheexample.conf

9) Setup cron job for recieve and update elements (e.g. https://github.com/DenisOgr/yii2-cronjobs):

* * * * * /home/admin/web/skid.kz/public_html/yii cron

10) Setup mysql wait_timeout=360

11) Setup php max_execution_time to 240 or 360

12) sudo apt-get install php-apc

13) Setup clean urls for host in your apache or nginx.

14) Setup apache config as exampled for API: docs/skid.kz.apacheexample.conf

15) Install tor (HOWTO http://help.ubuntu.ru/wiki/tor)

16) Setup proxy in config/params.php

17) sudo chmod 777 -R on runitme directory

Howto deploy to production
------------------------------------
skid.kz/deploy/bitbucket-hook-skid.kz.php

Howto setup developing environment
------------------------------------
1. Install git, vagrant, composer
2. Git clone this project
3. "composer install" 
4. "vagrant up" command from project root
4. Wait for Ansible infrastructure provisioning
5. "vagrant ssh" start ssh session on virtual machine and connect
6. Project files will be mounted in /vagrant folder. Files are syncing automatically

Howto test services
--------------------------------------

Test service: http://skid.kz/kupon/default/testapi?serviceId=1

Services ids:
1. ChocolifeApi
2. BlizzardApi
3. KupiKuponApi
4. MirKuponovApi
5. AutoKuponApi

You MUST specify TESTTYPE!!!
http://skid.kz/kupon/default/testapi?serviceId=1&testType=2

1. cities
2. categories
3. coupons for Almaty
4. coupons for Astana
5. advanced coupon for last coupon id
6. advanced coupon for last coupon id with call real update coupon
7. advanced coupon for first (oooold) coupon id
8. advanced coupon for first (oooold) coupon id with call real update coupon
9. advanced coupon for specified couponId

Example: http://skid.kz/kupon/default/testapi?serviceId=2&testType=9&couponId=17538

Howto use REST API
--------------------------------------
Base URL: http://api.skid.kz/v1/coupon

API documentation
- Info: https://github.com/yiisoft/yii2/blob/master/docs/guide/rest-quick-start.md, https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/rest-resources.md
- Search: http://stackoverflow.com/questions/25522462/yii2-rest-query#answer-25618361
- Sorting: http://stackoverflow.com/questions/26884449/sorting-data-in-default-rest-api-yii2-framework
