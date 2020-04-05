# 설치 가이드
설치 가이드의 모든 예시는 **우분투 18.04, apache2, php7.3** 용으로 작성되었으며 테스트했습니다.

## 준비
친토미는 웹서버 위에서 PHP를 통해 구동되므로 아래와 같은 프로그램들이 미리 준비되있어야 합니다.
* LAN또는 WAN으로 접근할 수 있는 컴퓨터 
* PHP를 지원하는 웹서버
* PHP 버전 7 (7.2 이상 권장)

## 친토미 다운로드
친토미 릴리즈는 [여기](https://github.com/Dictor/Chintomi/releases)에서 다운로드할 수 있습니다.
아래 예시처럼 릴리즈 소스코드를 다운받아 웹서버의 적절한 디렉터리에 압축을 풀어주세요.
```bash
mkdir /var/www/html/chintomi; cd /var/www/html/chintomi
wget https://github.com/Dictor/Chintomi/archive/1.1.3.tar.gz
tar -xf 1.1.3.tar.gz && rm 1.1.3.tar.gz
mv Chintomi-1.1.3/* ./ && rm -r Chintomi-1.1.3
```

## 의존성 설치
친토미가 요구하는 다음 목록의 PHP 빌트인 확장을 설치하거나 활성화시켜야 합니다.
- gd
- mbstring

```bash
sudo apt install php7.3-gd
sudo apt install php7.3-mbstring
```

서드-파티 의존성을 자동으로 설치하기 위해 [컴포저](https://getcomposer.org/)가 필요합니다. 
다음 명령어를 통해 필요한 의존성을 설치해야 합니다.
 ```bash
 composer install --no-dev
 ```
 
## 환경 설정
`config` 폴더의 `config.php` 파일을 적절하게 수정해야 합니다. 
필수적으로 변경해야하는 옵션에는 `PATH_COMICBOOK`, `PATH_JSON`이 있습니다.
```
nano ./config/config.php
```
## 관리자 계정 설정
환경 설정을 마친 후, 인터넷 브라우저를 통해 관리자 계정 설정 페이지에 접속합니다. 
예제대로 진행했다면 설정 페이지는 `<YOUR_SERVER_HOST>/chintomi/index.php?path=setup`가 됩니다.

## 라이브러리 생성
## 추가 기능 설정
