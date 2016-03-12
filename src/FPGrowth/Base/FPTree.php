<?php
namespace FPGrowth\Base;

class  FPTree
{
    private $_root;
    private $_routes = [];


    public function __construct()
    {
        $this->_root = new FPNode($this, null, null);
    }

    public function getRoot()
    {
        return $this->_root;
    }

    public function updateRoute($point)
    {
        if ($this != $point->getTree()) {
            throw new \Exception('Can not have a different tree');
        }
        $item = $point->getItem();
        if (isset($this->_routes[$item])) {
            $route = $this->_routes[$item];
            $route['tail']->setNeighbor($point);
            $this->_routes[$item] = ['head' => $route['head'], 'tail' => $point];
        } else {
            $this->_routes[$item] = ['head' => $point, 'tail' => $point];
        }
    }

    public function add($transaction)
    {
        $point = $this->_root;
        foreach ($transaction as $v) {
            $nextPoint = $point->search($v);
            if ($nextPoint) {
                $nextPoint->increment();
            } else {
                $nextPoint = new FPNode($this, $v);
                $point->add($nextPoint);
                $this->updateRoute($nextPoint);
            }
            $point = $nextPoint;
        }
    }

    public function getItems()
    {
        foreach ($this->_routes as $k => $v) {
            yield $k => $this->getNodes($k);
        }
    }

    public function getNodes($item)
    {
        $node = null;
        if (isset($this->_routes[$item]['head'])) {
            $node = $this->_routes[$item]['head'];
        } else {
            yield $node;
        }
        while ($node) {
            yield $node;
            $node = $node->getNeighbor();
        }
    }

    public function prefixPaths($item)
    {
        $collectPath = function ($node) {
            $path = [];
            while ($node && !$node->isRoot()) {
                $path[] = $node;
                $node = $node->getParent();
            }

            return array_reverse($path);
        };
        $ret = [];
        foreach ($this->getNodes($item) as $v) {
            $ret[] = $collectPath($v);
        }

        return $ret;
    }


    public function inspect()
    {
        echo "Tree:" . PHP_EOL;
        $this->_root->inspect(1);
        echo PHP_EOL;
        echo "Routes:" . PHP_EOL;
        foreach ($this->getItems() as $k => $v) {
            echo sprintf('   %s', $k) . PHP_EOL;
            foreach ($v as $v1) {
                echo sprintf('        %s', $v1->getItem()) . PHP_EOL;
            }
            break;
        }
    }
}