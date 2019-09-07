<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
?>
	<body>
		<?php 
			echo '<script>';
			echo 'function go_viewer(id){window.open("'.utl_htmldoc::GetHrefPath('PAGE_VIEWER').'/" + id);}';
			echo 'function go_list(page){location.href = "'.utl_htmldoc::GetHrefPath('PAGE_LIST').'/" + page;}';
			echo 'function logout(){var req = new XMLHttpRequest(); req.open("GET", "'.utl_htmldoc::GetHrefPath('PAGE_API').'" + "/logout", true); req.onload = function() {location.href = "'.utl_htmldoc::GetHrefPath('PAGE_INDEX').'";}; req.send();}; ';
			echo '</script>';
		?>
		<div class="list-group">
			<?php
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
						if (!is_null($res = ctr_list::GetBooks())) {
							ctr_list::DisplayBooks($res, $pagenum, $_SESSION['uname']);
						}
					}
				}
			?>
		</div>
	</body>