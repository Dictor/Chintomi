<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
?>
	<body>
		<script>
			function go_viewer(id){window.open("/viewer/" + id);}
			function go_list(page){location.href = "/list/" + page;}
		</script>
		<div class="list-group">
			<?php
				session_start();
				if(!ctr_list::CheckPermission()){
					utl_htmldoc::ShowError(403, "No access authority");
				} else {
					if (!array_key_exists(1, $urlargs)) {
						$pagenum = 1;
					} else {
						if ($urlargs[1] === 'action') {
							ctr_list::ProcessAction($urlargs[2]);
							return;
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
					}
					if (!is_null($res = ctr_list::GetBooks())) {
						ctr_list::DisplayBooks($res, $pagenum, $_SESSION['uname']);
					}
				}
			?>
		</div>
	</body>