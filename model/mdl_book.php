<?php
	namespace Dictor\Chintomi;
	require_once 'autoload.php';

	class Comicbook {
		public $name, $path, $id, $author, $imgcnt, $imgsize, $added_date;
		public function __construct($id, $path, $name, $author, $imgcnt, $imgsize, $added_date) {
			$this->name = $name;
			$this->path = $path;
			$this->id = $id;
			$this->author = $author;
			$this->imgcnt = $imgcnt;
			$this->imgsize = $imgsize;
			$this->added_date = $added_date;
		}
	}

	class mdl_book {
		private static $currentHandler = config::DB_HANDLER;
		const kindToColumn = ['name' => 'book_name', 'page' => 'image_count', 'size' => 'image_size', 'date' => 'added_date'];
		
		public static function SetDB(string $handler): void {
			self::$currentHandler = $handler;
		}
		
		public static function UseDB($path = null): int {
			if(is_null($path)) {
				switch (self::$currentHandler) {
	                case 'SQLITE': return hnd_SQLite::Open(config::PATH_SQLITE);
	                case 'JSON': return hnd_json::Open(config::PATH_JSON);
				}
			} else {
				switch (self::$currentHandler) {
	                case 'SQLITE': return hnd_SQLite::Open($path);
	                case 'JSON': return hnd_json::Open($path);
				}
			}
		}
		
		public static function GetBooks(string $query, com_sort_dropdown $sort_param): array {
			switch (self::$currentHandler) {
                case 'SQLITE': 
                	$query = '%'.$query.'%';
                	$order = self::kindToColumn[$sort_param->Kind].' '.($sort_param->Direction == 'u' ? 'ASC' : 'DESC');
                	return hnd_SQLite::ResultToComicbook(hnd_SQLite::Query('SELECT * FROM comicbook WHERE book_name LIKE :name ORDER BY '.$order, array("name" => $query)));
                case 'JSON':
                	//jsondb doesn't support like operator in where statement!!
					$src = hnd_json::ResultToComicbook(hnd_json::GetDB()
										->select('*')
										->from(hnd_json::TABLE_BOOK)
										->order_by(self::kindToColumn[$sort_param->Kind], $sort_param->Direction == 'u' ? hnd_json::GetDB()::ASC : hnd_json::GetDB()::DESC)
										->get());
					if (!empty($query)) {
						$res = array();
						foreach($src as $now_src) {
							if (strpos($now_src->name, $query) !== false) {
								$res[] = $now_src;	
							}
						}
					} else {
						$res = $src;
					}
					return $res;
			}
		}
		
		public static function GetAllBooks(): array {
			switch (self::$currentHandler) {
                case 'SQLITE': return hnd_SQLite::ResultToComicbook(hnd_SQLite::Query('SELECT * FROM comicbook', array()));
                case 'JSON': return hnd_json::ResultToComicbook(hnd_json::GetDB()->select('*')->from(hnd_json::TABLE_BOOK)->get());
			}
		}
		
		public static function AddBook(Comicbook $book): void {
			switch (self::$currentHandler) {
                case 'SQLITE':
					hnd_SQLite::Execute('INSERT INTO comicbook (book_path, book_name, book_author, image_count, image_size, added_date) VALUES (:bpath, :bname, :bauthor, :bimgcnt, :bimgsize, :bdate)', 
						array('bpath' => $book->path, 'bname' => $book->name, 'bauthor' => $book->author, 'bimgcnt' => $book->imgcnt, 'bimgsize' => $book->imgsize, 'bdate' => (new \DateTime('now'))->format(\DateTime::ATOM)));
					break;
				case 'JSON':
					hnd_json::GetDB()->insert(hnd_json::TABLE_BOOK, 
						[
							'book_id' => \time() + count(hnd_json::ResultToArray(hnd_json::GetDB()->select('*')->from(hnd_json::TABLE_BOOK)->get())),
							'book_path' => $book->path, 
							'book_name' => $book->name, 
							'book_author' => $book->author, 
							'image_count' => $book->imgcnt, 
							'image_size' => $book->imgsize, 
							'added_date' => (new \DateTime('now'))->format(\DateTime::ATOM)
						]
					);
					break;
			}
		}
		
		public static function DeleteAllBooks(): void {
			switch (self::$currentHandler) {
                case 'SQLITE': hnd_SQLite::Execute('DELETE FROM comicbook', array()); break;
                case 'JSON': hnd_json::GetDB()->delete()->from(hnd_json::TABLE_BOOK)->trigger(); break;
			}
		}
		
		public static function DeleteBookByPath(string $path): void {
			switch (self::$currentHandler) {
                case 'SQLITE': hnd_SQLite::Execute('DELETE FROM comicbook WHERE book_path=:bpath', array('bpath' => $path)); break;
                case 'JSON': hnd_json::GetDB()->delete()->from(hnd_json::TABLE_BOOK)->where(['book_path' => $path])->trigger(); break;
			}
		}
		
		public static function SearchBook(string $id): array {
			switch (self::$currentHandler) {
                case 'SQLITE': return hnd_SQLite::ResultToComicbook(hnd_SQLite::Query('SELECT * FROM comicbook WHERE book_id=:bid', array('bid' => $id)));
                case 'JSON': return hnd_json::ResultToComicbook(hnd_json::GetDB()->select('*')->from(hnd_json::TABLE_BOOK)->where(['book_id' => $id])->get());
			}
		}
		
		public static function GetHumanFileSize(int $bytes, int $decimals = 2): string {
		  $sz = 'BKMGTP';
		  $factor = floor((strlen($bytes) - 1) / 3);
		  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
		}
	}
?>