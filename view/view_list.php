<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
?>
	<body>
		<?php 
			echo '<script src="'.utl_htmldoc::GetHrefPath('PAGE_JS').'"></script>';
			echo '<script>';
			echo 'function go_viewer(id) {window.open("'.utl_htmldoc::GetHrefPath('PAGE_VIEWER').'/" + id);}';
			echo 'function go_list(page) {location.href = "'.utl_htmldoc::GetHrefPath('PAGE_LIST').'/" + page;}';
			echo 'function logout() {var req = new XMLHttpRequest(); req.open("GET", "'.utl_htmldoc::GetHrefPath('PAGE_API').'" + "/logout", true); req.onload = function() {location.href = "'.utl_htmldoc::GetHrefPath('PAGE_INDEX').'";}; req.send();}; ';
			echo 'function go_setting() {window.open("'.utl_htmldoc::GetHrefPath('PAGE_SETTING').'");}';
			echo '</script>';
		?>
		<div class="list-group">
			<?php
				$plist_allowshow = false;
				$plist_res = array();
				if (mdl_user::UseDB() != 0) {
					utl_htmldoc::ShowError(500, "DB Error");
					utl_htmldoc::CloseDocument();
				} else {
					if(!mdl_user::CheckPermission(config::PERMISSION_LEVEL_LIST)){
						utl_htmldoc::ShowError(403, "No access authority");
					} else {
						if (!array_key_exists(1, $urlargs)) {
							$pagenum = 1;
						} else {
							if(!ctype_digit($urlargs[1])) {
								utl_htmldoc::ShowError(400, "Invalid Parameter");
								return;
							} else {
								if ((int)$urlargs[1] >= 1) {
									$pagenum = (int)$urlargs[1];
								} else {
									utl_htmldoc::ShowError(400, "Invalid Parameter");
									return;
								}
							}
						}
						$plist_allowshow = true;
						$plist_res = array_key_exists('search', $_GET) ? ctr_list::GetBooks($_GET['search']) : ctr_list::GetBooks();
					}
				}
			?>
			<div class="toolbar">
				<div class="btn-group btn-logout">
					<button class="btn btn-light btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
						<span class="service-icon"><i class="icon-user"></i></span>
						<span id="toolbar-username"><?php echo $_SESSION['uname']; ?></span>
					</button>
					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" href="javascript:go_setting()">관리</a>
						<a class="dropdown-item" href="javascript:logout()">로그아웃</a>
					</div>
				</div>
				<span class="list-summary"><?php if (!is_null($plist_res)) echo (string)count($plist_res); ?>개의 결과</span>
				<div class="search-form-xsmall btn-group btn-logout">
					<button class="btn btn-light btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
						<span class="service-icon"><i class="icon-magnifier"></i></span>
					</button>
					<div class="dropdown-menu dropdown-menu-right">
						<input id="search-key-xsmall" type="text" class="form-control">
						<button class="btn btn-outline-secondary" type="button" onclick="pList.gotoSearch(1)">검색</button>
					</div>
				</div>
				<div class="search-form input-group">
					<input id="search-key" type="text" class="form-control">
					<div class="input-group-append">
						<button class="btn btn-outline-secondary" type="button" onclick="pList.gotoSearch(0)">검색</button>
					</div>
				</div>
			</div>
			<?php
			if (!is_null($plist_res)) ctr_list::DisplayBooks($plist_res, $pagenum, $_SESSION['uname']);
			?>
		</div>
	</body>