<?php
include 'FPGrowth/FPGrowth.php';
include 'FPGrowth/Base/FPNode.php';
include 'FPGrowth/Base/FPTree.php';

function loadCsv($file){
    $fp = fopen($file, 'r');
    $ret = [];
    while(!feof($fp)){
        $ret[] = fgetcsv($fp);
    }
    fclose($fp);
    return $ret;
}

$file = '../example/test1.csv';

$o = new \FPGrowth\FPGrowth();
foreach($o->findFrequentItemSets(loadCsv($file), 1) as $k=>$v){
    //echo "$k\t" . var_export($v) . PHP_EOL;

}
