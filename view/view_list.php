<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
?>
	<body>
		<?php echo '<script src="'.utl_htmldoc::GetHrefPath('PAGE_JS').'"></script>'; ?>
		<div class="list-group">
			<?php
				$plist_res = array();
				$perm_res = mdl_user::CheckPermission(config::PERMISSION_LEVEL_LIST);
				if (is_null($perm_res)){
					utl_htmldoc::ShowError(500, "DB Error");
					utl_htmldoc::CloseDocument();
				} else {
					if(!$perm_res){
						utl_htmldoc::ShowError(403, "No access authority");
						utl_htmldoc::CloseDocument();
					} else {
						if (!array_key_exists(1, $urlargs)) {
							$pagenum = 1;
						} else {
							if(!ctype_digit($urlargs[1])) {
								utl_htmldoc::ShowError(400, "Invalid Parameter");
								utl_htmldoc::CloseDocument();
							} else {
								if ((int)$urlargs[1] >= 1) {
									$pagenum = (int)$urlargs[1];
								} else {
									utl_htmldoc::ShowError(400, "Invalid Parameter");
									utl_htmldoc::CloseDocument();
								}
							}
						}
						$plist_sort_dropdown = new com_sort_dropdown(array_key_exists('sort', $_GET) ? $_GET['sort'] : 'nameu');
						$plist_res = array_key_exists('search', $_GET) ? ctr_list::GetBooks($_GET['search'], $plist_sort_dropdown) : ctr_list::GetBooks('', $plist_sort_dropdown);
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
						<a class="dropdown-item" href="javascript:pList.go_setting()">관리</a>
						<a class="dropdown-item" href="javascript:pList.logout()">로그아웃</a>
					</div>
				</div>
				<span class="list-summary"><?php if (!is_null($plist_res)) echo (string)count($plist_res); ?>개의 결과</span>
				<div class="search-form-xsmall btn-group btn-logout">
					<button class="btn btn-light btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
						<span class="service-icon"><i class="icon-magnifier"></i></span>
					</button>
					<?php echo $plist_sort_dropdown->Html(); ?>
					<div class="dropdown-menu dropdown-menu-right">
						<input id="search-key-xsmall" type="text" class="form-control" <?php echo array_key_exists('search', $_GET) ? 'value="'.$_GET['search'].'"' : '' ?>>
						<button class="btn btn-outline-secondary" type="button" onclick="javascript:pList.go_list(<?php echo $pagenum ?>, null, document.getElementById('search-key-xsmall').value)">검색</button>
					</div>
				</div>
				<div class="search-form input-group">
					<?php echo $plist_sort_dropdown->Html(); ?>
					<input id="search-key" type="text" class="form-control" <?php echo array_key_exists('search', $_GET) ? 'value="'.$_GET['search'].'"' : '' ?>>
					<div class="input-group-append">
						<button class="btn btn-outline-secondary" type="button" onclick="javascript:pList.go_list(<?php echo $pagenum ?>, null, document.getElementById('search-key').value)">검색</button>
					</div>
				</div>
			</div>
			<?php if (!is_null($plist_res)) ctr_list::DisplayBooks($plist_res, $pagenum, $_SESSION['uname']); ?>
		</div>
	</body>