<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
	echo '<script>var api_host = "'.utl_htmldoc::GetHrefPath('PAGE_API').'"; var server_mem = "'.ini_get('memory_limit').'";</script>';
?>
<script>
    function library_update(kind) {
        var api_path;
        switch(kind) {
            case 0:
                printres("전체 라이브러리 업데이트 작업을 시작합니다.");
                printres("서버 메모리 설정 : " + server_mem);
                api_path = "update_all";
                break;
            case 1:
                printres("만화책 라이브러리 업데이트 작업을 시작합니다.");
                api_path = "update_lib";
                break;
            case 2:
                printres("썸네일 일괄 라이브러리 업데이트 작업을 시작합니다.");
                printres("서버 메모리 설정 : " + server_mem);
                api_path = "update_th";
                break;
            case 3:
                printres("썸네일 순차 라이브러리 업데이트 작업을 시작합니다.");
                printres("서버 메모리 설정 : " + server_mem);
                thnext_task();
                return;
        }
        var req = new XMLHttpRequest();
        req.open("GET", api_host + "/" + api_path, true);
        req.onload = function() {
            if (req.status == 200) {
                var res = JSON.parse(req.response);
                printres("작업 완료! 결과 : " + (res['res'] == 'success' ? "성공" : "실패"));
                printres("메세지 : " + res['msg']);
                printres("만화책 추가 : " + res['lib_added'] + ", 만화책 제거 : " + res['lib_deleted'] + ", 썸네일 추가 : " + res['th_added']);
            } else {
                printres("작업 중 오류가 발생했습니다! : 응답 " + req.status);
            }
        };
        req.onerror = function() {
            printres("작업 중 오류가 발생했습니다!");
        }
        req.send();
    }
    
    function thnext_task() {
        var req = new XMLHttpRequest();
        req.open("GET", api_host + "/update_th_next", true);
        req.onload = function() {
            if (req.status == 200) {
                var res = JSON.parse(req.response);
                printres("작업 완료! 결과 : " + (res['res'] == 'success' ? "성공" : "실패"));
                printres("메세지 : " + res['msg'] + " 썸네일 추가 : " + res['th_added']);
                if (res['th_added'] != "id → -1") {
                    thnext_task();
                } else {
                    printres("순차 작업을 종료합니다!");
                }
            } else {
                printres("작업 중 오류가 발생했습니다! : 응답 " + req.status);
            }
        };
        req.onerror = function() {
            printres("작업 중 오류가 발생했습니다!");
        }
        req.send();
        
    }
    
    function printres(txt) {
        $("#result").html($("#result").html() + "<br>[" + new Date() + "]  " + txt);
        $("#result").scrollTop($("#result")[0].scrollHeight);
    }
</script>

<div class='padding-div'>
    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="javascript:library_update(0)">라이브러리 업데이트</button>
    <p>'라이브러리 업데이트'는 아래 두 버튼의 작업을 한꺼번에 실행합니다.</p>
    <button type="button" class="btn btn-secondary btn-lg btn-block" onclick="javascript:library_update(1)">만화책 라이브러리 업데이트</button>
    <button type="button" class="btn btn-secondary btn-lg btn-block" onclick="javascript:library_update(2)">썸네일 일괄 업데이트</button>
    <button type="button" class="btn btn-secondary btn-lg btn-block" onclick="javascript:library_update(3)">썸네일 순차 업데이트</button>
    <div id='result'></div>
</div>