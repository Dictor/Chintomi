<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
	echo '<script src="'.utl_htmldoc::GetHrefPath('PAGE_JS').'"></script>';
?>
<script>
    function change() {
        if ($("#newPw").val() != $("#newPwChk").val()) { /* global $ */
            alert("새 비밀번호와 새 비밀번호 확인이 일치하지 않습니다!");
        } else {
            var passform = new FormData();
            passform.append("nowpass", $("#nowPw").val());
            passform.append("newpass", $("#newPw").val());
            POSTApiReq("change_pw", passform, function(resp) {
                var res = JSON.parse(resp);
                if (res["res"] == "success") {
                    alert("비밀번호를 성공적으로 변경했습니다!");
                    location.reload();
                } else {
                    switch(res["msg"]) {
                        case "now pw incorrect": alert("현재 비밀번호가 올바르지 않습니다!"); break;
                        case "input invalid": alert("비밀번호가 규칙에 맞지 않습니다!"); break;
                        case "invalid params": alert("매개변수 오류"); break;
                        default: alert("알 수 없는 오류"); break;
                    }
                }
            }, function() {
                alert("작업도중 오류가 발생했습니다!");
            });
        }
    }
</script>

<div class='padding-div'>
    <input type="password" class="form-control" id="nowPw" placeholder="현재 비밀번호">
    <input type="password" class="form-control" id="newPw" placeholder="새 비밀번호">
    <input type="password" class="form-control" id="newPwChk" placeholder="새 비밀번호 확인">
    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="javascript:change()">비밀번호 변경</button>
</div>