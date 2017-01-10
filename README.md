# Autodeploy.io

This project it's a really simple POC.
Demo: https://www.youtube.com/watch?v=gwja3GySc3g

# Installation

## Dev

### Host

Edit /etc/hosts :

```
127.0.0.1       www.autodeploy.tld
```

### Nginx

Add `autodeploy.tld` in `/etc/nginx/sites-enabled`

```
server {
    listen 80;

    server_name www.autodeploy.tld;
    root /home/seb/projets/autodeploy/web;

    error_log /var/log/nginx/autodeploy.error.log;
    access_log /var/log/nginx/autodeploy.access.log;

    # strip app.php/ prefix if it is present
    rewrite ^/app_dev\.php/?(.*)$ /$1 permanent;

    location / {
        index app_dev.php;
        try_files $uri @rewriteapp;
    }

    location @rewriteapp {
        rewrite ^(.*)$ /app_dev.php/$1 last;
    }

    # pass the PHP scripts to FastCGI server from upstream phpfcgi
    location ~ ^/(app|app_dev|app_staging|config)\.php(/|$) {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param  HTTPS off;
    }
}
```

### Get dependencies
```
composer install
npm install
bower install
```

```
app/console init:acl
```

# Consumers

```
app/console rabbitmq:consumer -w authentification_check
app/console rabbitmq:consumer -w tasks_retrieve
app/console rabbitmq:consumer -w queue_run
```

## Import JMS Translations

```
php app/console translation:extract en --config=app
```

