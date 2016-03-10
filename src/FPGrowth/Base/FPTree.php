<?php
namespace FPGrowth\Base;

class  FPTree
{
    private $_root;
    private $_routes = [];
    public function __construct()
    {
        $this->_root = new FPNode($this, None, None);
    }
}