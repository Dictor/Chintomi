/* global API_PATH */

function GETApiReq(verb, okcb, errorcb) {
    var req = new XMLHttpRequest();
    req.open("GET", API_PATH["API"] + "/" + verb, true);
    req.onload = function() {
        if (req.status == 200) {
            okcb(req.response);
        } else {
            errorcb();
        }
    };
    req.send();
}

function POSTApiReq(verb, param, okcb, errorcb) {
    var req = new XMLHttpRequest();
    req.open("POST", API_PATH["API"] + "/" + verb, true);
    req.onload = function() {
        if (req.status == 200) {
            okcb(req.response);
        } else {
            errorcb();
        }
    };
    req.send(param);
}

var pList = {
    gotoSearch: function(buttonkind) {
        var id_by_kind = ["search-key", "search-key-xsmall"];
        location.href = API_PATH["LIST"] + (API_PATH["LIST"].includes("?") ? "&" : "?") + "search=" + document.getElementById(id_by_kind[buttonkind]).value;
    },
    go_viewer: function(id) {
        window.open(API_PATH["VIEWER"] + "/" + id);
    },
	go_list: function(page) {
	    location.href = API_PATH["LIST"] + "/" + page;
	},
	logout: function() {
	    GETApiReq("logout", function() {
	        location.href = API_PATH["INDEX"];
	    }, function() {
	        alert("작업도중 오류가 발생했습니다!");
	    })
	},
	go_setting: function() {
	    window.open(API_PATH["SETTING"]);
	}
};

var spSetting = {
    apiChangePassword: function () {
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
    },
    apiUpdateLibrary : function (kind) {
        var api_path;
        switch(kind) {
            case 0:
                this.splibPrintRes("전체 라이브러리 업데이트 작업을 시작합니다.");
                api_path = "update_all";
                break;
            case 1:
                this.splibPrintRes("만화책 라이브러리 업데이트 작업을 시작합니다.");
                api_path = "update_lib";
                break;
            case 2:
                this.splibPrintRes("썸네일 일괄 라이브러리 업데이트 작업을 시작합니다.");
                api_path = "update_th";
                break;
            case 3:
                this.splibPrintRes("썸네일 순차 라이브러리 업데이트 작업을 시작합니다.");
                this.apiUpdateThumbnailNext();
                return;
            case 4:
                if(confirm("정말로 라이브러리 초기화 합니까?")) {
                    this.splibPrintRes("라이브러리 초기화 작업을 시작합니다.");
                    api_path = "reset_lib";
                    break;
                } else {
                    return;
                }
        }
        GETApiReq(api_path, function(resp) { /*global GETApiReq*/
                var res = JSON.parse(resp);
                spSetting.splibPrintRes("작업 완료! 결과 : " + (res['res'] == 'success' ? "성공" : "실패"));
                spSetting.splibPrintRes("메세지 : " + res['msg']);
                spSetting.splibPrintRes("만화책 추가 : " + res['lib_added'] + ", 만화책 제거 : " + res['lib_deleted'] + ", 썸네일 추가 : " + res['th_added']);
            }, function() {
                spSetting.splibPrintRes("작업 중 오류가 발생했습니다!");
            });
    },
    apiUpdateThumbnailNext: function () {
        GETApiReq("update_th_next", function(resp) {
                var res = JSON.parse(resp);
                spSetting.splibPrintRes("작업 완료! 결과 : " + (res['res'] == 'success' ? "성공" : "실패"));
                spSetting.splibPrintRes("메세지 : " + res['msg'] + " 썸네일 추가 : " + res['th_added']);
                if (res['th_added'] != "id → -1") {
                    spSetting.apiUpdateThumbnailNext();
                } else {
                    spSetting.splibPrintRes("순차 작업을 종료합니다!");
                }
            }, function() {
                spSetting.splibPrintRes("작업 중 오류가 발생했습니다!");
            })
    },
    splibPrintRes: function (txt) {
        $("#result").html($("#result").html() + "<br>→ " + txt); /* global $ */
        $("#result").scrollTop($("#result")[0].scrollHeight);
    },
    apiDeleteUser: function (uname) {
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
    },
    apiMakeUser: function () {
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
                                case "cant make admin account": alert("관리자 계정을 생성할 수 없습니다!"); break;
                                default: alert("알 수 없는 오류"); break;
                            }
                        }
                }, function() {}); 
            }
    },
    apiChangePermission: function (uname) {
            var passform = new FormData();
            passform.append("uname", uname);
            passform.append("uper", prompt("새로운 권한 레벨을 입력해주세요 (0~999) : "));
            POSTApiReq("change_uper", passform, function(resp) {
                var res = JSON.parse(resp);
                if (res["res"] == "success") {
                    alert("권한을 성공적으로 변경했습니다!");
                    location.reload();
                } else {
                    switch(res["msg"]) {
                        case "invalid user name": alert("유효하지 않은 사용자 이름입니다!"); break;
                        case "input invalid": alert("조건에 맞지 않는 유효하지 않은 입력입니다!"); break;
                        case "cant change admin account": alert("관리자 계정은 변경할 수 없습니다!"); break;
                        case "invalid params": alert("매개변수 오류"); break;
                        case "no authority": alert("권한 없음"); break;
                        default: alert("알 수 없는 오류"); break;
                    }
                }
            }, function() {
                alert("작업도중 오류가 발생했습니다!");
            });
        
    }
};