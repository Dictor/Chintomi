<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
	echo '<script src="'.utl_htmldoc::GetHrefPath('PAGE_JS').'"></script>';
?>
<script>
    
</script>
<div class='padding-div'>
    <input type="text" required class="form-control half-size-input" id="add-uname" placeholder="사용자 ID">
    <input type="number" required min=0 max=999 class="form-control half-size-input" id="add-uper" placeholder="사용자 권한">
    <input type="password" required class="form-control" id="add-upass" placeholder="새 비밀번호">
    <input type="password" required class="form-control" id="add-upasschk" placeholder="새 비밀번호 확인">
    <button type="button" required class="btn btn-primary btn-lg btn-block" onclick="javascript:spSetting.apiMakeUser()">사용자 추가</button>
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