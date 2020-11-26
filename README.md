## step - step deploy server

1. composer install
2. copy .env.example jadi .env dan sesuaikan pengaturan .env
3. php artisan key:generate.
4. php artisan migrate
5. php artisan nas:link
6. php artisan db:seed
7. php artisan passport:keys
8. php artisan passport:client --password (note: jika ada inputan nama di command line isi dengan: "sipongi" huruf kecil semua dan user provider pilih "users").
9. npm install
10. npm run prod

## step - step install redis centos

```bash

### CentOS/RHEL 7
yum install epel-release

### CentOS/RHEL 6
rpm -Uvh http://download.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm

yum install redis

### CentOS/RHEL 7
systemctl enable redis
systemctl start redis

### CentOS/RHEL 6
chkconfig redis on
service redis restart

yum install php-pear php-devel

pecl install igbinary igbinary-devel redis

nano /etc/redis/redis.conf
### set:
### maxmemory 256mb
### maxmemory-policy allkeys-lru

systemctl restart redis

### add on php.ini
### extension=redis.so

```

## step - step install redis ubuntu 18.04 & 16.04 LTS

```bash

sudo apt-get update
sudo apt-get upgrade

sudo apt-get install redis-server

sudo systemctl enable redis-server.service

sudo nano /etc/redis/redis.conf
### set:
### maxmemory 256mb
### maxmemory-policy allkeys-lru

sudo systemctl restart redis-server.service

sudo apt-get install php-redis php-igbinary

### add on php.ini
### extension=redis.so

```

## step - step create cron untuk menjalankan command scheduler

- note cron buat permenit saja karena udah di handle fungsi apa aja yg akan di execute di sisi code waktunya.
- sudo crontab -e

```bash

* * * * * sudo -u www-data php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1

```

- systemctl restart cron

## step - step Supervisor for queue worker ubuntu

1. sudo apt-get install supervisor
2. nano /etc/supervisor/conf.d/laravel-sipongi-worker.conf
3. example code :

```bash
    [program:laravel-sipongi-horizon]
    process_name=%(program_name)s
    command=php /path-to-your-project/artisan horizon
    autostart=true
    autorestart=true
    user=www-data
    redirect_stderr=true
    stdout_logfile=/path-to-your-project/storage/logs/laravel-horizon.log
    stopwaitsecs=43200
```

4. sudo supervisorctl reread
5. sudo supervisorctl update
6. sudo supervisorctl start all
7. supervisorctl restart all

## step - step optimize performance postgresql

```bash
    ALTER SYSTEM SET work_mem = '1GB';
    ALTER SYSTEM SET maintenance_work_mem = '1GB';
    SELECT pg_reload_conf();
    SHOW work_mem;

    restart service postgresql
```
