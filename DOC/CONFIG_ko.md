# 환경 설정 가이드
친토미의 환경 설정은 `config/config.php`에서 설정할 수 있습니다. 이 문서는 각각의 옵션을 자세히 설명합니다.

## 필수적 설정 옵션
아래 네가지 옵션들은 친토미의 정상 구동을 위해 초기에 설정해줘야하는 옵션들입니다.

### PATH_COMICBOOK
만화책 데이터가 들어있는 디렉터리의 경로입니다. 
친토미는 여기서 지정된 디렉터리에 존재하는 모든 하위 디렉터리와 파일을 검색하여 라이브러리를 생성할 것 입니다.
이때 서버에 운영체제에 맞는 주소 형식을 사용하야합니다. (예를 들어 윈도우는 `\`와 `/` 모두 경로 구분자로 사용할 수 있지만 유닉스에서는 `/`만 가능합니다.)
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 문자열 | 임의의 절대경로 | `"/home/ubuntu/environment/Chintomi/data"` |

### DB_HANDLER
라이브러리를 관리할 때 사용하는 데이터베이스 핸들러를 선택합니다. `JSON`은 composer을 통한 의존성 설치를 통해 바로 사용할 수 있으며, `SQLITE`는 `sqlite3` PHP 글로벌 확장의 활성화가 필요합니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 문자열 | `"JSON"` 또는 `"SQLITE"` | `"JSON"` |

### PATH_SQLITE
데이터베이스 핸들러가 `SQLITE`으로 지정된 경우, 데이터베이스 파일이 생성될 경로입니다.
친토미는 이 경로에 데이터베이스 파일을 읽고 씁니다. 핸들러가 `JSON`으로 설정된 경우 이 옵션은 무시됩니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 문자열 | 임의의 절대경로 | `"/home/ubuntu/environment/Chintomi/data/chintomi.db"` |

### PATH_JSON
데이터베이스 핸들러가 `JSON`으로 지정된 경우, 데이터베이스가 생성될 디렉터리입니다.
친토미는 이 디렉터리 하위에 데이터베이스 파일들을 읽고 씁니다. 핸들러가 `SQLITE`으로 설정된 경우 이 옵션은 무시됩니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 문자열 | 임의의 절대경로 | `"/home/ubuntu/environment/Chintomi/data/db"` |

## 선택적 설정 옵션
### ALLOWED_EXTENSION
### RESIZEIMG_ENABLE
### RESIZEIMG_THRESHOLD
### PERMISSION_LEVEL_ADMIN
### PERMISSION_LEVEL_VIEWER
### PERMISSION_LEVEL_LIST
### INPUT_VALIDATION_USERNAME
### INPUT_VALIDATION_PASSWORD
### LIST_PAGIGATION_ENABLE
### LIST_PAGIGATION_THRESHOLD
### THUMBNAIL_DISPLAY_ENABLE
### THUMBNAIL_LONGSIDE_LENGTH
### THUMBNAIL_QUALITY
### URLREWRITE_ENABLE
### MEMORY_UNLIMIT_UPDATE_THUMBNAIL
