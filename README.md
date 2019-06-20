# camagru

## About this project
This is my first web project and I wanted well beginning. <br />
That's why I learn mvc and factory design pattern. <br />
So this webapp implement mvc design pattern and my own ORM (`/camagru/dao/`).<br />
(Of course my app is containerized with Docker). <br />

Subject: [here](camagru.fr.pdf)

## How to start it

Start docker env with:  `docker-compose up` 

`common/img` is the file that contains image database,<br />
So you need to change permission for docker container can write on it. (chmod)

## Accept camera if you run on http:
with chrome: `/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --unsafely-treat-insecure-origin-as-secure="http://192.168.99.100" --user-data-dir=~/Desktop`

## Setup Mail:
Set your Gmail ids in `conf/php/ssmtp/ssmtp.conf`

## Setup databases: 

Start php setup file with: 
`docker exec php php config/setup.php`


## MYSQL

connect to mysql: `docker run -it --network <name_of_parent_file>_camagru --rm mysql mysql -hmysql -uroot -p`


## NGINX

Reload nginx configuration: `docker container exec nginx nginx -s reload`  
Test nginx configuration file: `docker container exec nginx nginx -t`  


#### Edit nginx configuration

Edit files in `conf/nginx`  


## PHP

Edit php-fpm configuration files in `conf/php/etc`  


## LOGS

#### Errors
Use `tail -f conf/nginx/logs/nginx/error.log`  
fastcgi errors are redirected by nginx to this file.  

#### Access
Nginx acces logs ca be seen using:  
`tail -f conf/nginx/logs/nginx/access.log`
