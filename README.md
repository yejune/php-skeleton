# php skeleton

Phalcon Micro Mvc Framework Wrapper.

### Environment
macOS, php7, Phalcon 3.0.x, docker.

### Require Bootapp

Helps docker-based development in macOS.

```
wget https://raw.githubusercontent.com/yejune/bootapp/master/bootapp.phar
chmod +x bootapp.phar
mv bootapp.phar /usr/local/bin/bootapp
```

### install

```
git clone https://github.com/yejune/php-skeleton project
cd project
bootapp up
bootapp task composer install
```

### File structure

```
home/
    app/
        controllers/
        helpers/
        middlewares/
        models/
        specs/
        traits/
        views/
    public/
        css/
        img/
        js/
        .htaccess
        index.php
    Bootfile.yml
    composer.jsonphp-cs-fixer.settings
    README.md
    routes.yml
```

-   Follow PSR rules
    -   php-cs-fixer.settings
-   Pass all requests to index.php
-   Namespace \App corresponds to /home/app folder
-   Route and validate are defined in app/specs/swagger.json
-   Bootapp.yml bootapp config
