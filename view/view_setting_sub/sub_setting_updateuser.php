<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
	echo '<script src="'.utl_htmldoc::GetHrefPath('PAGE_JS').'"></script>';
?>
<script>
    function delusr(uname) {
        if(confirm("정말로 '" + uname + "' 계정을 삭제합니까?")) {
           var pform = new FormData();
            pform.append("uname", uname);
            POSTApiReq("delete_user", pform, function(resp) {
                    var res = JSON.parse(resp);
                    if (res["res"] == "success") {
                        alert("계정을 성공적으로 삭제했습니다");
                    } else {
                        switch(res["msg"]) {
                            case "invalid user name": alert("유효하지 않은 사용자 이름입니다!"); break;
                            case "cant delete admin account": alert("관리자 계정은 삭제할 수 없습니다!"); break;
                            case "invalid params": alert("매개변수 오류"); break;
                            case "no authority": alert("권한 없음"); break;
                            default: alert("알 수 없는 오류"); break;
                        }
                    }
                }, function() {}); 
        }
    }
</script>
<div class='padding-div'>
    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="">사용자 추가</button>
</div>
<table class="table table-hover">
    <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">비밀번호</th>
          <th scope="col">권한</th>
          <th scope="col">관리</th>
        </tr>
    </thead>
    <tbody>
        <?php ctr_setting::spDisplayUser() ?>
    </tbody>
</table>