<?php>
function chintomi_autoloader($className){
  $path = '';
  switch (substr($className), 0, 3) {
        case 'ctr':
            path = 'controller/'.$className.'.php';
            break;
        case 'mdl':
            path = 'model/'.$className.'.php';
            break;
        case 'utl':
            path = 'util/'.$className.'.php';
            break;
        case 'hnd':
            path = 'util/DBhandler.php';
            break;
        case 'con':
            path = 'config/config.php';
            break;
  }
  require path;
}
spl_autoload_register('chintomi_autoloader');
<?>