<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
	echo '<script src="'.utl_htmldoc::GetHrefPath('PAGE_JS').'"></script>';
?>
<div class='padding-div'>
    <input type="password" class="form-control" id="nowPw" placeholder="현재 비밀번호">
    <input type="password" class="form-control" id="newPw" placeholder="새 비밀번호">
    <input type="password" class="form-control" id="newPwChk" placeholder="새 비밀번호 확인">
    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="javascript:spSetting.apiChangePassword()">비밀번호 변경</button>
</div>