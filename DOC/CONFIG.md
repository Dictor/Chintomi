# Preferences guide
Chintomi's environment settings can be set in `config/config.php`. This article describes each option in detail.

## Required options
The four options below are the options that must be set initially for the normal operation of Chin Tommy.

### PATH_COMICBOOK
The path to the directory containing comic book data.
Chintomi will create a library by searching all subdirectories and files in the directory specified here.
In this case, you must use the address format that matches the operating system of the server. (For example, Windows can use both `\` and `/` as path separators, but Unix only `/`.) Also, the web server must have read access to this directory.
| type | allowed | default |
|:---:|:---:|:------:|
| string | any absolute path | `"/chintomi/books"` |

### DB_HANDLER
Select the database handler used to manage the library. `JSON` is ready to use by installing dependencies through composer, `SQLITE` requires the `sqlite3` PHP extension to be enabled.
| type | allowed | default |
|:---:|:---:|:------:|
| string | `"JSON"` or `"SQLITE"` | `"JSON"` |

### PATH_SQLITE
If the database handler is specified as `SQLITE`, this is the path where the database file will be created.
Chintomi reads and writes database files to this path. This option is ignored if the handler is set to `JSON`.
At this point, the directory must already exist except for the files set in the path. (In default, up to `home/ubuntu/environment/Chintomi/data/`)
| type | allowed | default |
|:---:|:---:|:------:|
| string | any absolute path | `"/chintomi/library"` |

### PATH_JSON
If the database handler is specified as `JSON`, this is the directory where the database will be created.
Chin Tommy reads and writes database files under this directory. This option is ignored if the handler is set to `SQLITE`.
At this point, the directory must already exist except for the files set in the path. (In default, up to `home/ubuntu/environment/Chintomi/data/`)
| type | allowed | default |
|:---:|:---:|:------:|
| string | any absolute path | `"/chintomi/library"` |

## Optional options
### ALLOWED_EXTENSION
This is a list of extensions that Chintomi will treat as image files. It only recognizes extensions in this list.
| type | allowed | default |
|:---:|:---:|:------:|
| array of strings | extension string | `array("jpg", "jpeg", "png", "bmp", "gif")` |

### RESIZEIMG_ENABLE
Whether image resizing is enabled. If enabled, resize all image files that exceed the threshold size.
This can be useful for low bandwidth situations or for images that are too large. However, it may take a long time to process the image.
| type | allowed | default |
|:---:|:---:|:------:|
| boolean | TRUE or FALSE | `FALSE` |

### RESIZEIMG_THRESHOLD
Image resizing threshold size. If the number of pixels on the longer side of the image exceeds this value, the image is resized.
| type | allowed | default |
|:---:|:---:|:------:|
| integer | assignee integer | `1200` |

### PERMISSION_LEVEL_*
Permission level for each function. If the user's permission level is higher than or equal to that value, the feature is available.
| name | type | allowed | default | function |
|:---:|:---:|:---:|:----:|:----:|
| PERMISSION_LEVEL_ADMIN | integer | assignee integer | `999` | Administrative Features |
| PERMISSION_LEVEL_VIEWER | integer | assignee integer | `2` | View Comics |
| PERMISSION_LEVEL_LIST | integer | assignee integer | `2` | Look up comic book list |

### INPUT_VALIDATION_USERNAME
This is a regular expression that validates all user name input such as user creation and login. If the input does not satisfy this regular expression, the request is rejected. If you don't want to validate the string, you can use `/.*/`. default is a regular expression that allows only 4 to 12 character strings including uppercase and lowercase letters and numbers only.
| type | allowed | default |
|:---:|:---:|:------:|
| string | PCRE Compliant Regular Expressions | `'/^[A-Za-z0-9]{4,12}$/'` |

### INPUT_VALIDATION_PASSWORD
This is a regular expression that validates all password input such as user creation and login. default is a regular expression that allows only 4 to 15 character strings containing at least one special character, uppercase and lowercase letters, and three numbers, respectively.
| type | allowed | default |
|:---:|:---:|:------:|
| string | PCRE Compliant Regular Expressions | `'/^.*(?=^.{4,15}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$% ^&+=]).*$/'` |

### LIST_PAGIGATION_ENABLE
Whether to enable pagination for comic book lists. When activated, it is divided into pages as much as the number of thresholds and can be viewed, and when deactivated, it is displayed at once without division of pages.
| type | allowed | default |
|:---:|:---:|:------:|
| boolean | TRUE or FALSE | `TRUE` |

### LIST_PAGIGATION_THRESHOLD
The maximum number of comic books that make up a page for comic book list pagination.
| type | allowed | default |
|:---:|:---:|:------:|
| integer | assignee integer | `15` |

### THUMBNAIL_DISPLAY_ENABLE
Whether to display comic book thumbnails in the comic book list.
| type | allowed | default |
|:---:|:---:|:------:|
| boolean | TRUE or FALSE | `TRUE` |

### THUMBNAIL_LONGSIDE_LENGTH
The size of the thumbnail image. The maximum length of the longer side of the width or length.
| type | allowed | default |
|:---:|:---:|:------:|
| integer | assignee integer | `150` |

### THUMBNAIL_QUALITY
The percentage number of jpeg quality of the thumbnail image.
| type | allowed | default |
|:---:|:---:|:------:|
| integer | assignee integer | `85` |

### URL_SUBPATH_ENABLE
Whether to enable or not specify the prefix of Chin Tommy's scripts, images, and URLs used when requesting when using a reverse proxy.
| type | allowed | default |
|:---:|:---:|:------:|
| boolean | TRUE or FALSE | `FALSE` |

### URL_SUBPATH
The prefix value when using URL prefixes.
| type | allowed | default |
|:---:|:---:|:------:|
| string | Any URL path without trailing slash | `"/chintomi"` |

### URLREWRITE_ENABLE
Whether to enable the URL rewrite feature. When enabled, the address is simplified like `/index.php?path=viewer/1/1` → `/viewer/1/1`. When activated, URL rewriting must be set in the web server like [.htaccess](/.htaccess).
| type | allowed | default |
|:---:|:---:|:------:|
| boolean | TRUE or FALSE | `FALSE` |

### MEMORY_UNLIMIT_UPDATE_THUMBNAIL
Ignore php.ini settings on thumbnail update and turn off PHP memory limit. Can be used when an out-of-memory error occurs.
| type | allowed | default |
|:---:|:---:|:------:|
| boolean | TRUE or FALSE | `FALSE` |
