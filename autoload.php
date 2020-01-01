<?php
namespace Dictor\Chintomi; 
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
            $path = 'handler/'.$rawclassname.'.php';
            break;
        case 'han':
            $path = 'handler/handler.php';
            break;
        case 'con':
            $path = 'config/config.php';
            break;
        default: printf("[Chintomi autoloader] Cannet find matched class from '%s'\n", $className); return;
  }
  require $path;
}
spl_autoload_register('Dictor\\Chintomi\\chintomi_autoloader');
?>