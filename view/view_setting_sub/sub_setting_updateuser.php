<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
?>
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