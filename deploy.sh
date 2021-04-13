#!/bin/sh
# deploy.sh
# Easy deployment to multiple servers.
# Deploy code, files, settings and much more servers via ssh.
# Laravel deploy files

STAGE="prod"
today=`date '+%Y_%m_%d__%H_%M'`;
bkpFileName="bkp_$today.tar.gz"

#----------- Move ENV  -------------------
sudo cp "$STAGE.env" .env
sudo chmod -R 777 bootstrap
sudo chmod -R 777 storage
sudo chmod -R 777 vendor
#composer install --prefer-dist
#composer dumpautoload
#composer update
php artisan migrate
#php artisan db:seed
#php artisan key:generate
#php artisan migrate --seed

#----------- File And Folder Permission -------

php artisan storage:link
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan optimize:clear
#----------- Remove Unused Files --------------
rm -rf tests
rm -rf dev.env
#rm -rf prod.env
rm -rf example.env
rm -rf readme.md
rm -rf .env.example


