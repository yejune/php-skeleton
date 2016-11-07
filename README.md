## skeleton

Phalcon Micro Mvc Framework의 Wrapper이다.


### 환경
macOS, php7, Phalcon 3.0.x, docker 사용.

### bootapp 필요
macOS에서 docker 기반 개발에 도움을 줌.
```
wget https://raw.githubusercontent.com/yejune/bootapp/master/bootapp.phar
chmod +x bootapp.phar
mv bootapp.phar /usr/local/bin/bootapp
```

### 설치
```
git clone https://github.com/yejune/skeleton project
cd project
bootapp up
```

### 구조
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

> PSR-1, PSR-2를 따라 클래스 이름은 반드시 `Studlycaps`

> PSR-4 Autoloader 규칙을 따르며 네임스페이스 \App는 /home/app 폴더에 대응.

> 모든 요청을 index.php로 전달.

> route.yml  route 설정 파일

> Bootapp.yml docker config