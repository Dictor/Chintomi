<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
?>
	<body class="viewer">
		<div class="container">
			<?php
				session_start();
				if(!ctr_viewer::CheckPermission()){
					utl_htmldoc::ShowError(403, "No access authority");
				} else {
					if(empty($urlargs[1]) or !ctype_digit(($urlargs[1]))) {
						utl_htmldoc::ShowError(400, "Invalid Parameter");
					} else {
						if (empty($urlargs[2])) {
							ctr_viewer::ShowImage($urlargs[1], '1');
						} else if (!ctype_digit($urlargs[2])) {
							utl_htmldoc::ShowError(400, "Invalid Parameter");
						} else {
							ctr_viewer::ShowImage($urlargs[1], $urlargs[2]);
						}
					}
				}
			?>
		</div>
	</body>