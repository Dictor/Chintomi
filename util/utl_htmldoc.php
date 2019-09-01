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
                case 'PAGE_API': return config::URLREWRITE_ENABLE ? '/api' : '/index.php?path=api';
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
    			case 200: $string = 'OK'; break;
    			case 400: $string = 'Bad Request'; break;
    			case 401: $string = 'Unauthorized'; break;
    			case 403: $string = 'Forbidden'; break;
    			case 404: $string = 'Not Found'; break;
    			case 408: $string = 'Request Timeout'; break;
    			case 410: $string = 'Gone'; break;
    			case 414: $string = 'Request-URI Too Long'; break;
    			case 500: $string = 'Internal Server Error'; break;
    			case 503: $string = 'Service Unavailable'; break;
    			default: $string = 'Unknown';  break;
    		}
    		return $string;
        }
    }
?>