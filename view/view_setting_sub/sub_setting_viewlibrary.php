<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
?>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
              <th scope="col">책 ID</th>
              <th scope="col">책 이름</th>
              <th scope="col">책 경로</th>
              <th scope="col">이미지 개수</th>
              <th scope="col">이미지 용량</th>
              <th scope="col">생성 시간</th>
            </tr>
        </thead>
        <tbody>
            <?php ctr_setting::spDisplayLibrary() ?>
        </tbody>
    </table>
</div>