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
                        location.reload();
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
    
    function makeusr() {
            if ($("#add-upass").val() != $("#add-upasschk").val()) { /* global $ */
                alert("새 비밀번호와 새 비밀번호 확인이 일치하지 않습니다!");
            } else {
                var pform = new FormData();
                pform.append("uname", $("#add-uname").val());
                pform.append("upass", $("#add-upass").val());
                pform.append("uper", $("#add-uper").val());
                POSTApiReq("make_user", pform, function(resp) {
                        var res = JSON.parse(resp);
                        if (res["res"] == "success") {
                            alert("계정을 성공적으로 생성했습니다");
                            location.reload();
                        } else {
                            switch(res["msg"]) {
                                case "input invalid": alert("조건에 맞지 않는 유효하지 않은 입력입니다!"); break;
                                case "already exist user": alert("이미 존재하는 유저입니다!"); break;
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
    <input type="text" required class="form-control half-size-input" id="add-uname" placeholder="사용자 ID">
    <input type="number" required min=0 max=999 class="form-control half-size-input" id="add-uper" placeholder="사용자 권한">
    <input type="password" required class="form-control" id="add-upass" placeholder="새 비밀번호">
    <input type="password" required class="form-control" id="add-upasschk" placeholder="새 비밀번호 확인">
    <button type="button" required class="btn btn-primary btn-lg btn-block" onclick="javascript:makeusr()">사용자 추가</button>
</div>
<table class="table table-hover">
    <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">권한</th>
          <th scope="col">관리</th>
        </tr>
    </thead>
    <tbody>
        <?php ctr_setting::spDisplayUser() ?>
    </tbody>
</table>