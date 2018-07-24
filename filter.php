<?php
require 'vendor/autoload.php'; // Autoload
use ado\TFilter;

$filter1 = new TFilter('data','=','2007-06-02');
echo $filter1->dump();



?>