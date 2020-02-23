<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
	
	class com_sort_dropdown {
	    const SORT_KIND = ['name', 'page', 'size'];
	    const SORT_KIND_STRING = ['name' => '이름', 'page' => '페이지 수', 'size' => '크기']; // This array will be removed when i18n is implemented.
	    
	    public $Kind, $Direction;
	    
	    public function __construct($param) {
	    	// every expected input of $param is only english, so there is no necessity to deal with multi-byte string.
	        $this->Kind = substr($param, 0, strlen($param) - 1);
	        $this->Direction = substr($param, strlen($param) - 1, 1);
	        
	        if (!in_array($this->Kind, self::SORT_KIND)) $this->Kind = 'name';
	        if ($this->Direction !== 'u' and $this->Direction !== 'd') $this->Direction = 'u';
	    }
	    
	    public function Html(): string {
	        $res = '';
	        for ($i = 0; $i < count(self::SORT_KIND); $i++) {
	        	$now_dir = (self::SORT_KIND[$i] == $this->Kind ? ($this->Direction === 'u' ? 'd' : 'u') : $this->Direction);
	            $res .= '<a class="dropdown-item" onclick="pList.go_query(null, \''.self::SORT_KIND[$i].$now_dir.'\')">'.self::SORT_KIND_STRING[self::SORT_KIND[$i]].($now_dir == 'u' ? '▲' : '▼').'</a>';
	        }
	        $now_sort = self::SORT_KIND_STRING[$this->Kind].($this->Direction === 'u' ? '▲' : '▼');
	        return <<<HTML
	        <div class="btn-group btn-sort">
				<button class="btn btn-light btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
					<span id="toolbar-username">$now_sort</span>
				</button>
				<div class="dropdown-menu dropdown-menu-right">
					$res
				</div>
			</div>
HTML;
	    }
	}
?>