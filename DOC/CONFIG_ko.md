# 환경 설정 가이드
친토미의 환경 설정은 `config/config.php`에서 설정할 수 있습니다. 이 문서는 각각의 옵션을 자세히 설명합니다.

## 필수적 설정 옵션
아래 네가지 옵션들은 친토미의 정상 구동을 위해 초기에 설정해줘야하는 옵션들입니다.

### PATH_COMICBOOK
만화책 데이터가 들어있는 디렉터리의 경로입니다. 
친토미는 여기서 지정된 디렉터리에 존재하는 모든 하위 디렉터리와 파일을 검색하여 라이브러리를 생성할 것 입니다.
이때 서버의 운영체제에 맞는 주소 형식을 사용하야합니다. (예를 들어 윈도우는 `\`와 `/` 모두 경로 구분자로 사용할 수 있지만 유닉스에서는 `/`만 가능합니다.) 또한, 웹서버가 이 디렉토리에 대해 읽기 권한을 가지고 있어야 합니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 문자열 | 임의의 절대경로 | `"/chintomi/books"` |

### DB_HANDLER
라이브러리를 관리할 때 사용하는 데이터베이스 핸들러를 선택합니다. `JSON`은 composer을 통한 의존성 설치를 통해 바로 사용할 수 있으며, `SQLITE`는 `sqlite3` PHP 확장의 활성화가 필요합니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 문자열 | `"JSON"` 또는 `"SQLITE"` | `"JSON"` |

### PATH_SQLITE
데이터베이스 핸들러가 `SQLITE`으로 지정된 경우, 데이터베이스 파일이 생성될 경로입니다.
친토미는 이 경로에 데이터베이스 파일을 읽고 씁니다. 핸들러가 `JSON`으로 설정된 경우 이 옵션은 무시됩니다.
이때 경로에 설정된 파일을 제외한 디렉터리가 이미 존재해야합니다. (기본값에서는 `home/ubuntu/environment/Chintomi/data/`까지)
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 문자열 | 임의의 절대경로 | `"/chintomi/library"` |

### PATH_JSON
데이터베이스 핸들러가 `JSON`으로 지정된 경우, 데이터베이스가 생성될 디렉터리입니다.
친토미는 이 디렉터리 하위에 데이터베이스 파일들을 읽고 씁니다. 핸들러가 `SQLITE`으로 설정된 경우 이 옵션은 무시됩니다.
이때 경로에 설정된 파일을 제외한 디렉터리가 이미 존재해야합니다. (기본값에서는 `home/ubuntu/environment/Chintomi/data/`까지)
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 문자열 | 임의의 절대경로 | `"/chintomi/library"` |

## 선택적 설정 옵션
### ALLOWED_EXTENSION
친토미가 이미지 파일로 취급할 확장자 목록입니다. 이 목록의 확장자에 대해서만 인식합니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 문자열의 배열 | 확장자 문자열 | `array("jpg", "jpeg", "png", "bmp", "gif")` |

### RESIZEIMG_ENABLE
이미지 리사이즈 활성화 여부입니다. 활성화 된 경우, 임계 크기를 넘는 이미지 파일을 전부 리사이징합니다.
저대역폭 상황이나 너무 큰 이미지에 대해서 유용할 수 있습니다. 다만, 이미지를 처리하는데 긴 시간이 걸릴 수 있습니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 부울 | TRUE 또는 FALSE | `FALSE` |

### RESIZEIMG_THRESHOLD
이미지 리사이즈 임계 크기입니다. 이미지의 가로와 세로 중 더 긴 변의 픽셀 수가 이 값을 넘으면 이미지가 리사이즈됩니다. 
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 정수 | 양수인 정수 | `1200` |

### PERMISSION_LEVEL_*
각 기능에 대한 권한 수준입니다. 사용자의 권한 레벨이 해당 값 이상이면 해당 기능을 사용할 수 있습니다.
| 이름 | 유형 | 가능한 값 | 기본값 | 기능 |
|:---:|:---:|:---:|:----:|:----:|
| PERMISSION_LEVEL_ADMIN | 정수 | 양수인 정수 | `999` | 관리 기능 |
| PERMISSION_LEVEL_VIEWER | 정수 | 양수인 정수 | `2` | 만화책 보기 |
| PERMISSION_LEVEL_LIST | 정수 | 양수인 정수 | `2` | 만화책 목록 조회 |

### INPUT_VALIDATION_USERNAME
사용자 생성, 로그인등 모든 사용자 이름 입력에 대해 검증하는 정규식입니다. 만약 입력이 이 정규식을 만족하지 않는다면 요청은 거부됩니다. 문자열을 검증하지 않고 싶다면 `/.*/`을 사용할 수 있습니다. 기본값은 영대소문자와 숫자만을 포함한 4자이상 12자 이하의 문자열만 허용하는 정규식입니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 문자열 | PCRE 호환 정규식 | `'/^[A-Za-z0-9]{4,12}$/'` |

### INPUT_VALIDATION_PASSWORD
사용자 생성, 로그인등 모든 비밀번호 입력에 대해 검증하는 정규식입니다. 기본값은 특수문자와 영대소문자와 숫자 세가지 종류를 각각 반드시 하나 이상 포함하는 4자 이상 15자 이하 문자열만 허용하는 정규식입니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 문자열 | PCRE 호환 정규식 | `'/^.*(?=^.{4,15}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&+=]).*$/'` |

### LIST_PAGIGATION_ENABLE
만화책 목록에 대한 페이지네이션 기능 활성화 여부입니다. 활성화시 임계값 갯수만큼 페이지로 구분되어 조회할 수 있고, 비활성화시 페이지 구분없이 한번에 표시됩니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 부울 | TRUE 또는 FALSE | `TRUE` |

### LIST_PAGIGATION_THRESHOLD
만화책 목록 페이지네이션에 대해 한 페이지를 구성하는 최대 만화책 갯수입니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 정수 | 양수인 정수 | `15` |

### THUMBNAIL_DISPLAY_ENABLE
만화책 목록에서 만화책 썸네일을 표시할 지 여부입니다. 
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 부울 | TRUE 또는 FALSE | `TRUE` |

### THUMBNAIL_LONGSIDE_LENGTH
썸네일 이미지의 크기입니다. 가로와 세로 중 더 긴변의 최대 길이입니다. 
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 정수 | 양수인 정수 | `150` |

### THUMBNAIL_QUALITY
썸네일 이미지의 jpeg 품질 백분율 수치입니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 정수 | 양수인 정수 | `85` |

### URL_SUBPATH_ENABLE
역방향 프록시등을 사용시 친토미의 스크립트, 이미지, 요청시 사용되는 URL의 접두사를 지정 활성화 여부입니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 부울 | TRUE 또는 FALSE | `FALSE` |

### URL_SUBPATH
URL 접두사 사용시 접두사 값입니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 문자열 | 후행 슬래시 없는 임의의 URL 경로 | `"/chintomi"` |

### URLREWRITE_ENABLE
URL 재작성 기능 활성화 여부입니다. 활성화시 `/index.php?path=viewer/1/1` → `/viewer/1/1`과 같이 주소가 단순해집니다. 활성화 시 [.htaccess](/.htaccess)와 같이 웹서버에서 URL 재작성 설정이 되있어야합니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 부울 | TRUE 또는 FALSE | `FALSE` |

### MEMORY_UNLIMIT_UPDATE_THUMBNAIL
썸네일 업데이트시 php.ini 설정을 무시하고 PHP 메모리 제한을 해제합니다. 메모리 부족 오류가 발생할 시 사용할 수 있습니다.
| 유형 | 가능한 값 | 기본값 |
|:---:|:---:|:------:|
| 부울 | TRUE 또는 FALSE | `FALSE` |