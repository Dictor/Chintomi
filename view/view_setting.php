<?php 
	namespace Dictor\Chintomi; 
	require_once 'autoload.php';
?>
	<body>
	    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <span class="navbar-brand mb-0 h1">Chintomi 관리</span>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                        <?php
            				if(!mdl_user::CheckPermission(0)){
            					utl_htmldoc::ShowError(403, "No access authority");
            				} else {
            					ctr_setting::DisplayNavbarItem($_SESSION['uname']);
            				}
            			?>
                </ul>
            </div>
        </nav>
	</body>