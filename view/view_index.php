<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
?>
	<body>
		<?php
			session_start();
			ctr_index::CheckUser((array_key_exists('uname', $_POST) ? $_POST['uname'] : NULL), (array_key_exists('upass', $_POST) ? $_POST['upass'] : NULL));
		?>
		<form action="./index" method="post">
			<div class="login-box">
				<span colspan=2 class="login-box-title">Chintomi</span>
				<table class="login-box-input">
					<tr>
						<td><input name="uname" class="form-control form-control-sm" type="text" placeholder="아이디" tabindex="1"></td>
						<td rowspan=2><button type="submit" class="btn btn-primary" tabindex="3">로그인</button></td>
					</tr>
					<tr>
						<td><input name="upass" class="form-control form-control-sm" type="password" placeholder="비밀번호" tabindex="2"><td>
					</tr>
				</table>
			</div>
		</form>
	</body>