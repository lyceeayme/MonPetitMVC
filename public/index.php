<?php

require '..\vendor\autoload.php';

use App\Controller\TestAutoload;
use App\Exceptions\AppException;
/**
 * Description of index
 *
 * @author ayme.pignon
 */
$e = new TestAutoload();
echo $e->composer();
