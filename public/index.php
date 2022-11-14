<?php
use App\Controller\TestAutoload;
require '..\vendor\autoload.php';
/**
 * Description of index
 *
 * @author ayme.pignon
 */
$e = new TestAutoload();
echo $e->composer();
