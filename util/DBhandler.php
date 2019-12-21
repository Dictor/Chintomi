<?php
	namespace Dictor\Chintomi;
	
    interface handler {
		public static function Open($path);
		public static function Close();
		public static function Execute(string $preQuery, array $parameter);
		public static function Query(string $preQuery, array $parameter);
		public static function ResultToArray($res);
		public static function ResultToComicbook($res);
	}
	
	class hndStatement {
		const TYPE_EQUAL = 0;
		const TYPE_LIKE = 1;
		
		const HANDLER_SQLITE = 0;
		const HANDLER_JSON = 1;
		
		private $params = array();
		private $where_clause_count = 0;
		
		public function select() {
			$this->params['action'] = 'select';
			return $this;
		}
		
		public function insert(array $data) {
			$this->params['action'] = 'insert';
			$this->params['insert_data'] = $data;
			return $this;
		}
		
		public function update(string $column_name, string $value) {
			$this->params['action'] = 'update';
			$this->params["update_col_name"] = $column_name;
			$this->params["update_col_val"] = $value;
			return $this;
		}
		
		public function delete() {
			$this->params['action'] = 'delete';
			return $this;
		}
		
		public function table(string $table_name) {
			$this->params['table'] = $table_name;
			return $this;
		}
		
		public function where(int $clause_type, string $column_name, string $value, string $merge = 'AND') {
			$this->params[$this->where_clause_count] = [$clause_type, $column_name, $value, $merge];
			$this->where_clause_count++;
			return $this;
		}
		
		public function toRealStatement(int $db_type) {
			switch($db_type) {
				case self::HANDLER_JSON:
					return $this->params;
				case self::HANDLER_SQLITE:
					$rtn_string = '';
					switch($this->params['action']) {
						case 'insert':
							$arr = self::toArrayExpr($this->params['insert_data']);
							$rtn_string .= 'INSERT INTO '.$this->params['table'].' ('.$arr['col'].') VALUES ('.$arr['val'].')';
							break;
						case 'select':
							$rtn_string .= 'SELECT * FROM '.$this->params['table'];
							if ($this->where_clause_count > 0) $rtn_string .= ' WHERE '.$this->makeWhereStmt();
							break;
						case 'update':
							/* Update statement unused in chintomi currently */
							break;
						case 'delete':
							$rtn_string .= 'DELETE * FROM '.$this->params['table'];
							if ($this->where_clause_count > 0) $rtn_string .= ' WHERE '.$this->makeWhereStmt();
							break;
					}
					return $rtn_string;
			}
		}
		
		private static function toArrayExpr(array $arr) {
			$value_col = '';
			$value_val = '';
			foreach ($arr as $col => $val) {
				$value_col .= $col.', ';
				$value_val .= self::toSqlExpr($val).', ';
			}
			$value_col = trim($value_col, ', ');
			$value_val = trim($value_val, ', ');
			return ['col' => $value_col, 'val' => $value_val];
		}
		
		private static function toSqlExpr($var) {
			return (gettype($var) == "string" ? "'".$var."'" : strval($var));
		}
		
		private function makeWhereStmt() {
			$condition = ''; 
			for ($i = 0; $i < $this->where_clause_count; $i++) {
				$condition .= ' '.$this->params[$i][3].' '.$this->params[$i][1];
				$condition .= ($this->params[$i][0] == self::TYPE_EQUAL ? ' = ' : ' LIKE ').self::toSqlExpr($this->params[$i][2]);
			}
			$condition = trim($condition, ' AND ');
			$condition = trim($condition, ' OR ');
			return $condition;
		}
	}
	
	class hnd_SQLite implements handler{
		private static $currentDB;
		private static $isOpen = FALSE;
		
		public static function Open($path) {
			try{
				if(!is_dir(dirname($path))) mkdir(dirname($path), 0777, TRUE);
				if(!is_file($path)){
					$db = new \SQLite3($path);
					$db->exec('CREATE TABLE comicbook(book_id INTEGER PRIMARY KEY AUTOINCREMENT, book_path TEXT NOT NULL, book_name TEXT, book_author TEXT, image_count INTEGER, image_size INTEGER, added_date TEXT);');
					$db->exec('CREATE TABLE user(user_name TEXT PRIMARY KEY NOT NULL, user_pass TEXT NOT NULL, user_permission INTEGER NOT NULL);');
					$db->close();
				}
				
				if(!self::$isOpen){
					$db = new \SQLite3($path);
					self::$currentDB = $db;
					if ($db->lastErrorCode() == 0) self::$isOpen = TRUE;
					return $db->lastErrorCode();	
				} else {
					return 0;
				}
			} catch(\Throwable $t) {
				return -1;
			}
		}
		
		public static function Close() {
			self::$currentDB->close();
		}
		
		public static function Execute(string $preQuery, array $parameter) {
			return self::Query($preQuery, $parameter);
		}
		
		public static function Query(string $preQuery, array $parameter) {
			$state = self::$currentDB->prepare($preQuery);
			if($state == FALSE) return FALSE;
			foreach($parameter as $nowkey => $nowval) {
				$state->bindValue($nowkey, $nowval);
			}
			return $state->execute();
		}
		
		public static function ResultToArray($res) {
			$arr = array();
			while ($nowrow = $res->fetchArray(SQLITE3_ASSOC)){
				if($nowrow != FALSE) $arr[] = $nowrow;
			}
			return $arr;
		}
		
		public static function ResultToComicbook($res) {
			$arr = array();
			while ($nowrow = $res->fetchArray()) {
				$arr[]=new Comicbook($nowrow['book_id'], $nowrow['book_path'], $nowrow['book_name'], $nowrow['book_author'], $nowrow['image_count'], $nowrow['image_size'], $nowrow['added_date']);
			}
			return $arr;
		}
	}
?>