# Install guild


## Docker (Recommended)
Chintomi Docker distribution is based on the image [trafex/alpine-nginx-php7](https://hub.docker.com/r/trafex/alpine-nginx-php7), so **php-fpm 7.3, nginx 1.18** and all necessary extensions are included and no extra work required.

**Docker**:
```
sudo docker run -p 80:8080 \
                -v /path/for/database:/chintomi/library \
                -v /path/to/books:/chintomi/books \
                kimdictor/chintomi
```
**Docker compose**: 
```
chintomi:
  image: kimdictor/chintomi
  ports:
    - "80:8080"
  volumes:
    - /path/for/database:/chintomi/library
    - /path/to/books:/chintomi/books
```

## Binary 
**This description is based on Ubuntu 18.04, apache2, php7.3**.
### Requirements
Chintomy runs through PHP on the web server, so the following programs must be prepared in advance.
* Computers with LAN or WAN access
* Web server that supports PHP
* PHP version 7 (7.2 or higher recommended)

### Download binary
Chintomi releases can be downloaded from [here](https://github.com/Dictor/Chintomi/releases).
Download the release source code and extract it to an appropriate directory on the web server.
```bash
mkdir /var/www/html/chintomi; cd /var/www/html/chintomi
wget https://github.com/Dictor/Chintomi/archive/1.1.3.tar.gz
tar -xf 1.1.3.tar.gz && rm 1.1.3.tar.gz
mv Chintomi-1.1.3/* ./ && rm -r Chintomi-1.1.3
```

### Install dependency
You will need to install or enable the PHP built-in extensions listed below list.
- gd
- mbstring

```bash
sudo apt install php7.3-gd
sudo apt install php7.3-mbstring
```

[Composer](https://getcomposer.org/) is required to automatically install third-party dependencies.
After installing Composer, you install dependencies through Composer.
```bash
sudo apt install composer
composer install --no-dev
```
 
## Preferences
As in the example below, you need to edit the `config.php` file in the `config` folder appropriately with a text editor.
```
nano ./config/config.php
```
Options that must be changed are `PATH_COMICBOOK`, `PATH_JSON`.
For the description for parameters of the configuration file, refer to the [Configuration Guide](CONFIG.md). When using docker, you just need to adjust the path of the bind volume appropriately instead of chanding comicbook path.

### Set up admin account
After setting the environment, access the administrator account settings page through your Internet browser.
If you followed the example, the address of the setup page is `<PATH_TO_CHINTOMI>/index.php?path=setup` and you need to set up an administrator account on this page.

### Create comicbook library
After the initial setting is complete, create a library by accessing `<PATH_TO_CHINTOMI>/index.php?path=setting`.

#### Troubleshooting
- If `500 - DB Error (Open failure)` is displayed: Set the `PATH_*` option in `config.php` correctly, check that the set directory exists, and ensure that the web server daemon has permission to access the directory. If not, you need to create the directory or set the permissions correctly.
