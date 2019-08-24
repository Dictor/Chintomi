<?php
    namespace Dictor\Chintomi;

    class utl_htmldoc {
        public function GetHrefPath(string $key) {
            switch ($key) {
                case 'PAGE_LIST': return config::URLREWRITE_ENABLE ? '/list' : '/index.php?path=list';
                case 'PAGE_SETTING': return config::URLREWRITE_ENABLE ? '/setting' : '/index.php?path=setting';
                case 'PAGE_LIST_LOGOUT': return config::URLREWRITE_ENABLE ? '/list/action/logout' : '/index.php?path=list/action/logout';
                case 'PAGE_VIEWER': return config::URLREWRITE_ENABLE ? '/viewer' : '/index.php?path=viewer';
                case 'PAGE_INDEX': return config::URLREWRITE_ENABLE ? '/index' : '/index.php';
                case 'PAGE_SETUP': return config::URLREWRITE_ENABLE ? '/setup' : '/index.php?path=setup';
            }
        }
        
        public function ShowError(int $errcode, $errdesc){
        	http_response_code($errcode);
        	echo self::GetErrorDivContent((string)$errcode." ".self::HTTPCodeToString($errcode), $errdesc);
        }
        
        public function CloseDocument() {
            echo '</body></html>';
            exit();
        }
        
        public static function GetErrorDivContent(string $title, string $desc) {
            return '<div class="error-box"><p class="error-title">'.$title.'</p><p class="error-desc">'.$desc.'</p><div>';
        }
        
        public static function HTTPCodeToString($code){
            switch( $code ) {
    			// 1xx Informational
    			case 100: $string = 'Continue'; break;
    			case 101: $string = 'Switching Protocols'; break;
    			case 122: $string = 'Request-URI too long'; break; // Microsoft
    	
    			// 2xx Success
    			case 200: $string = 'OK'; break;
    			case 201: $string = 'Created'; break;
    			case 202: $string = 'Accepted'; break;
    			case 203: $string = 'Non-Authoritative Information'; break; // HTTP/1.1
    			case 204: $string = 'No Content'; break;
    			case 205: $string = 'Reset Content'; break;
    			case 206: $string = 'Partial Content'; break;
    	
    			// 3xx Redirection
    			case 300: $string = 'Multiple Choices'; break;
    			case 301: $string = 'Moved Permanently'; break;
    			case 302: $string = 'Found'; break;
    			case 303: $string = 'See Other'; break; //HTTP/1.1
    			case 304: $string = 'Not Modified'; break;
    			case 305: $string = 'Use Proxy'; break; // HTTP/1.1
    			case 306: $string = 'Switch Proxy'; break; // Depreciated
    			case 307: $string = 'Temporary Redirect'; break; // HTTP/1.1
    	
    			// 4xx Client Error
    			case 400: $string = 'Bad Request'; break;
    			case 401: $string = 'Unauthorized'; break;
    			case 402: $string = 'Payment Required'; break;
    			case 403: $string = 'Forbidden'; break;
    			case 404: $string = 'Not Found'; break;
    			case 405: $string = 'Method Not Allowed'; break;
    			case 406: $string = 'Not Acceptable'; break;
    			case 407: $string = 'Proxy Authentication Required'; break;
    			case 408: $string = 'Request Timeout'; break;
    			case 409: $string = 'Conflict'; break;
    			case 410: $string = 'Gone'; break;
    			case 411: $string = 'Length Required'; break;
    			case 412: $string = 'Precondition Failed'; break;
    			case 413: $string = 'Request Entity Too Large'; break;
    			case 414: $string = 'Request-URI Too Long'; break;
    			case 415: $string = 'Unsupported Media Type'; break;
    			case 416: $string = 'Requested Range Not Satisfiable'; break;
    			case 417: $string = 'Expectation Failed'; break;
    			case 426: $string = 'Upgrade Required'; break;
    			case 449: $string = 'Retry With'; break; // Microsoft
    			case 450: $string = 'Blocked'; break; // Microsoft
    	
    			// 5xx Server Error
    			case 500: $string = 'Internal Server Error'; break;
    			case 501: $string = 'Not Implemented'; break;
    			case 502: $string = 'Bad Gateway'; break;
    			case 503: $string = 'Service Unavailable'; break;
    			case 504: $string = 'Gateway Timeout'; break;
    			case 505: $string = 'HTTP Version Not Supported'; break;
    			case 506: $string = 'Variant Also Negotiates'; break;
    			case 509: $string = 'Bandwidth Limit Exceeded'; break; // Apache
    			case 510: $string = 'Not Extended'; break;
    	
    			// Unknown code:
    			default: $string = 'Unknown';  break;
    		}
    		return $string;
        }
    }
?>