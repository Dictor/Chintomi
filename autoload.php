<?php
function chintomi_autoloader($className){
  $path = '';
  $rawclassname = str_replace('Dictor\\Chintomi\\', '', $className);
  switch (substr($rawclassname, 0, 3)) {
        case 'ctr':
            $path = 'controller/'.$rawclassname.'.php';
            break;
        case 'mdl':
            $path = 'model/'.$rawclassname.'.php';
            break;
        case 'utl':
            $path = 'util/'.$rawclassname.'.php';
            break;
        case 'hnd':
            $path = 'util/DBhandler.php';
            break;
        case 'con':
            $path = 'config/config.php';
            break;
  }
  require $path;
}
spl_autoload_register('chintomi_autoloader');
?>