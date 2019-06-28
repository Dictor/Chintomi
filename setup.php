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
            require_once 'model/mdl_user.php';
        
            if (mdl_user::UseDB() != 0) {
                echo 'DB Error!';
            } else {
                if(is_null($has_admin = mdl_user::CheckAdminExist())) {
                    echo 'DB Error!';
                } else if(!$has_admin) {
                    if (!is_null($uname = (array_key_exists('uname', $_POST) ? $_POST['uname'] : NULL)) and !is_null($upass = (array_key_exists('upass', $_POST) ? $_POST['upass'] : NULL))) {
                        if(mdl_user::MakeAdmin($uname, $upass) != FALSE) {
                            echo '<script>alert("설정 완료!"); location.href="./";</script>';
                        } else {
                            echo 'DB Error!';
                        }
                    } else {
                        print 
<<<SETUPHTML
                        <form action="./setup.php" method="post">
            			<div class="login-box">
            				<span colspan=2 class="login-box-title">Chintomi</span>
            				<table class="login-box-input">
            					<tr>
            						<td><input name="uname" class="form-control form-control-sm" type="text" placeholder="관리자 ID"></td>
            						<td rowspan=2><button type="submit" class="btn btn-primary">등록</button></td>
            					</tr>
            					<tr>
            						<td><input name="upass" class="form-control form-control-sm" type="password" placeholder="관리자 PW"><td>
            					</tr>
            				</table>
            			</div>
            		</form>
SETUPHTML;
                    }
                    
                } else {
                    echo '이미 관리자 계정 설정이 완료되었습니다. setup.php를 삭제해주세요!';
                }
            }
    ?>
	</body>
</html>
	
