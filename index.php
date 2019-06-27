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
	</head>
	
	<body>
		<?php
			require_once 'controller/ctr_index.php';
			session_start();
		
			ctr_index::CheckUser((array_key_exists('uname', $_SESSION) ? $_SESSION['uname'] : NULL), (array_key_exists('uname', $_POST) ? $_POST['uname'] : NULL), (array_key_exists('upass', $_POST) ? $_POST['upass'] : NULL));
		?>
		<form action="/" method="post">
			<div class="login-box">
				<span colspan=2 class="login-box-title">Chintomi</span>
				<table class="login-box-input">
					<tr>
						<td><input name="uname" class="form-control form-control-sm" type="text" placeholder="아이디"></td>
						<td rowspan=2><button type="submit" class="btn btn-primary">로그인</button></td>
					</tr>
					<tr>
						<td><input name="upass" class="form-control form-control-sm" type="password" placeholder="비밀번호"><td>
					</tr>
				</table>
			</div>
		</form>
	</body>
</html>