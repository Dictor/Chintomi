<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
	echo '<script>var api_host = "'.utl_htmldoc::GetHrefPath('PAGE_API').'";</script>';
?>
<script>
    function change() {
        if ($("#newPw").val() != $("#newPwChk").val()) {
            alert("새 비밀번호와 새 비밀번호 확인이 일치하지 않습니다!");
        } else {
            
        }
    }
</script>

<div class='padding-div'>
    <input type="password" class="form-control" id="nowPw" placeholder="현재 비밀번호">
    <input type="password" class="form-control" id="newPw" placeholder="새 비밀번호">
    <input type="password" class="form-control" id="newPwChk" placeholder="새 비밀번호 확인">
    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="javascript:change()">비밀번호 변경</button>
</div>