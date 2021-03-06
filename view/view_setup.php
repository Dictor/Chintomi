<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
?>
	<body>
	    <?php
            if (mdl_user::UseDB() != 0) {
                utl_htmldoc::ShowError(500, "DB Error (Open failure)");
                utl_htmldoc::CloseDocument();
            } else {
                if(is_null($has_admin = mdl_user::CheckAdminExist())) {
                    utl_htmldoc::ShowError(500, "DB Error (Cannot load admin info)");
                    utl_htmldoc::CloseDocument();
                } else if(!$has_admin) {
                    if (!is_null($uname = (array_key_exists('uname', $_POST) ? $_POST['uname'] : NULL)) and !is_null($upass = (array_key_exists('upass', $_POST) ? $_POST['upass'] : NULL))) {
                        if(!preg_match(Config::INPUT_VALIDATION_USERNAME, $uname) or !preg_match(Config::INPUT_VALIDATION_PASSWORD, $upass)){
                            utl_htmldoc::ShowError(400, "Invalid user name or password");
                            utl_htmldoc::CloseDocument();
                        } else {
                            if((array_key_exists('upasschk', $_POST) ? $_POST['upasschk'] : NULL) === $upass){
                                if(mdl_user::MakeAdmin($uname, $upass) != FALSE) {
                                    echo '<script>alert("설정 완료! 이제 setup.php를 삭제해주세요."); location.href="'.utl_htmldoc::GetHrefPath('PAGE_INDEX').'";</script>';
                                } else {
                                    utl_htmldoc::ShowError(500, "DB Error (Cannot made admin account)");
                                    utl_htmldoc::CloseDocument();
                                }
                            } else {
                                 echo '<script>alert("비밀번호와 비밀번호 확인이 일치하지 않습니다."); location.href="'.utl_htmldoc::GetHrefPath('PAGE_SETUP').'";</script>';
                            }
                        }
                    }
                } else {
                    utl_htmldoc::ShowError(410, 'Admin account already set. <br> PLEASE DELETE "setup.php"');
					utl_htmldoc::CloseDocument();
                }
            }
        ?>
        <form action="<?php echo utl_htmldoc::GetHrefPath('PAGE_SETUP'); ?>" method="post">
            <div class="setup-box">
            	<span colspan=2 class="login-box-title">Chintomi 관리자 설정</span>
            	<table class="login-box-input">
            		<tr>
            			<td><input name="uname" class="form-control form-control-sm" type="text" placeholder="관리자 ID" tabindex="1"></td>
            			<td rowspan=3><button type="submit" class="btn btn-primary" tabindex="4">등록</button></td>
            		</tr>
            		<tr>
            			<td><input name="upass" class="form-control form-control-sm" type="password" placeholder="관리자 PW" tabindex="2"><td>
            		</tr>
            		<tr>
            			<td><input name="upasschk" class="form-control form-control-sm" type="password" placeholder="관리자 PW 확인" tabindex="3"><td>
            		</tr>
            	</table>
            </div>
        </form>
	</body>