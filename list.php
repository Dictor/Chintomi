<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		  <title>Chintomi</title>
		
		<link href="style/standard.css" rel="stylesheet">
		<link href="https://fonts.googleapis.com/earlyaccess/nanumgothic.css" rel="stylesheet" type="text/css">
		
		<!--BootStrap-->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
		
		<link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.min.css" rel="stylesheet">
		
		<script>
			function go_viewer(id){window.open("./viewer.php?book_id=" + id);}
			function go_list(page){location.href = "./list.php?page=" + page;}
		</script>
	</head>
	
	<body>
		<div class="list-group">
			<?php
				require_once 'controller/ctr_list.php';
				session_start();
				if(!ctr_list::CheckPermission()){
					Util::ShowError(403, "No access authority");
				} else {
					if (!array_key_exists('page', $_GET)) {
						$pagenum = 1;
					} else {
						if(!ctype_digit($_GET['page'])) {
							Util::ShowError(400, "Invalid Parameter");
							return;
						} else {
							if ((int)$_GET['page'] >= 1) {
								$pagenum = (int)$_GET['page'];
							} else {
								Util::ShowError(400, "Invalid Parameter");
								return;
							}
						}
					}
					if (!is_null($res = ctr_list::GetBooks())) {
						if (array_key_exists('action', $_GET)) {
							ctr_list::ProcessAction($_GET['action']);
							return;
						}
						ctr_list::DisplayBooks($res, $pagenum, $_SESSION['uname']);
					}
				}
			?>
		</div>
	</body>
</html>