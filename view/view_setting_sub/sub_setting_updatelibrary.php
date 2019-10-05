<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
	echo '<script src="'.utl_htmldoc::GetHrefPath('PAGE_JS').'"></script>';
?>
<div class='padding-div'>
    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="javascript:spSetting.apiUpdateLibrary(0)">라이브러리 업데이트</button>
    <p>'라이브러리 업데이트'는 '만화책 라이브러리 업데이트'와 '썸네일 일괄 업데이트' 작업을 한번에 실행합니다.</p>
    <button type="button" class="btn btn-secondary btn-lg btn-block" onclick="javascript:spSetting.apiUpdateLibrary(1)">만화책 라이브러리 업데이트</button>
    <button type="button" class="btn btn-secondary btn-lg btn-block" onclick="javascript:spSetting.apiUpdateLibrary(2)">썸네일 일괄 업데이트</button>
    <p>'썸네일 순차 업데이트'는 많은 만화책의 썸네일을 한번에 생성할 때 권장됩니다.</p>
    <button type="button" class="btn btn-secondary btn-lg btn-block" onclick="javascript:spSetting.apiUpdateLibrary(3)">썸네일 순차 업데이트</button>
    <div id='result'></div>
</div>