# 설치 가이드


## 도커 (권장됨)
친토미의 도커 배포판은 [trafex/alpine-nginx-php7](https://hub.docker.com/r/trafex/alpine-nginx-php7) 이미지를 기반으로 제작되었기 때문에 *php-fpm 7.3, nginx 1.18* 및 필요한 확장이 모두 포함되어 별도의 작업이 필요없습니다.   

**도커**:
```
sudo docker run -p 80:8080 \
                -v /path/for/database:/chintomi/library \
                -v /path/to/books:/chintomi/books \
                kimdictor/chintomi
```
**도커 컴포즈**: 
```
chintomi:
  image: kimdictor/chintomi
  ports:
    - "80:8080"
  volumes:
    - /path/for/database:/chintomi/library
    - /path/to/books:/chintomi/books
```

## 바이너리 
**우분투 18.04, apache2, php7.3** 기준으로 설명합니다.  
### 준비
친토미는 웹서버 위에서 PHP를 통해 구동되므로 아래와 같은 프로그램들이 미리 준비되있어야 합니다.
* LAN또는 WAN으로 접근할 수 있는 컴퓨터 
* PHP를 지원하는 웹서버
* PHP 버전 7 (7.2 이상 권장)

### 친토미 다운로드
친토미 릴리즈는 [여기](https://github.com/Dictor/Chintomi/releases)에서 다운로드할 수 있습니다.
릴리즈 소스코드를 다운받아 웹서버의 적절한 디렉터리에 압축을 풀어주세요.
```bash
mkdir /var/www/html/chintomi; cd /var/www/html/chintomi
wget https://github.com/Dictor/Chintomi/archive/1.1.3.tar.gz
tar -xf 1.1.3.tar.gz && rm 1.1.3.tar.gz
mv Chintomi-1.1.3/* ./ && rm -r Chintomi-1.1.3
```

### 의존성 설치
친토미가 요구하는 다음 목록의 PHP 빌트인 확장을 설치하거나 활성화시켜야 합니다.
- gd
- mbstring

```bash
sudo apt install php7.3-gd
sudo apt install php7.3-mbstring
```

서드-파티 의존성을 자동으로 설치하기 위해 [컴포저](https://getcomposer.org/)가 필요합니다. 
컴포저를 설치한 후, 컴포저를 통해 의존성을 설치합니다.
```bash
sudo apt install composer
composer install --no-dev
```
 
## 환경 설정
아래의 예시처럼 텍스트 편집기로 `config` 폴더의 `config.php` 파일을 적절하게 수정해야 합니다. 
```
nano ./config/config.php
```
필수적으로 변경해야하는 옵션에는 `PATH_COMICBOOK`, `PATH_JSON`이 있습니다.
환경 설정 파일의 매개변수들에 대해서는 [환경 설정 가이드](CONFIG_ko.md)를 참고하세요. 도커 사용시 bind 볼륨의 경로를 적절히 조정하면됩니다.

### 관리자 계정 설정
환경 설정을 마친 후, 인터넷 브라우저를 통해 관리자 계정 설정 페이지에 접속합니다. 
예제대로 진행했다면 설정 페이지의 주소는 `<PATH_TO_CHINTOMI>/index.php?path=setup`이며 이 페이지에서 관리자 계정을 설정해야합니다. 

### 라이브러리 생성
초기설정이 끝났다면 `<PATH_TO_CHINTOMI>/index.php?path=setting`에 접속하여 라이브러리를 생성하면 됩니다.

#### 문제 해결
- `500 - DB Error (Open failure)`가 표시되는 경우 : `config.php`에서 `PATH_*` 옵션을 올바르게 설정하고 설정한 디렉터리가 존재하는지, 웹서버 데몬이 해당 디렉터리에 접근할 권한이 있는 지 확인하고 그렇지 않은 경우 디렉터리를 생성하거나 권한을 올바르게 설정해야합니다. 
