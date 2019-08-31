<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
?>
<table class="table table-hover">
    <thead>
        <tr>
          <th scope="col">변수 이름</th>
          <th scope="col">변수 값</th>
        </tr>
    </thead>
    <tbody>
        <?php ctr_setting::spDisplayConfig() ?>
    </tbody>
</table>