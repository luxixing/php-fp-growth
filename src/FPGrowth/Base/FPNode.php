<?php
/**
 * A node in an FP tree
 * @author xixing.lu@outlook.com
 * @version 2016-03-16
 */
namespace FPGrowth\Base;

class  FPNode
{
    private $_tree;
    private $_item;
    private $_count;
    private $_parent = null;
    private $_children = [];
    private $_neighbor = null;

    public function __construct($tree, $item, $count = 1)
    {
        $this->_tree = $tree;
        $this->_item = $item;
        $this->_count = $count;
    }

    public function isRoot()
    {
        return empty($this->_item) && empty($this->_count);
    }

    public function isLeaf()
    {
        return empty($this->_children);
    }

    public function getTree()
    {
        return $this->_tree;
    }

    public function getItem()
    {
        return $this->_item;
    }

    public function getCount()
    {
        return $this->_count;
    }

    public function setCount($n)
    {
        return $this->_count += $n;
    }

    public function getParent()
    {
        return $this->_parent;
    }

    public function setParent($node)
    {
        if ($node !== null && (!$node instanceof FPNode)) {
            throw new \Exception('A node must have an FPNode as a parent.');
        }
        if ($node && $node->getTree() !== $this->_tree) {
            throw new \Exception('Cannot have a parent from another tree.');

        }
        $this->_parent = $node;
    }

    public function getNeighbor()
    {
        return $this->_neighbor;
    }

    public function setNeighbor($node)
    {
        if ($node !== null && (!$node instanceof FPNode)) {

            throw new \Exception('A node must have an FPNode as a neighbor.');
        }
        if ($node && $node->getTree() !== $this->_tree) {

            throw new \Exception('Cannot have a neighbor from another tree.');
        }
        $this->_neighbor = $node;
    }

    public function getChildren()
    {
        return $this->_children;
    }


    public function isContain($item)
    {
        return isset($this->_children[$item]);
    }

    public function add($child)
    {
        if (!$child instanceof FPNode) {
            throw new \Exception('Can only add other FPNodes as children');
        }
        $item = $child->getItem();
        if (!isset($this->_children[$item])) {
            $this->_children[$item] = $child;
            $child->setParent($this);
        }
    }

    public function search($item)
    {
        return isset($this->_children[$item]) ? $this->_children[$item] : null;
    }

    public function increment()
    {
        if ($this->_count === null) {
            throw new \Exception('Root nodes have no associated count.');
        }
        $this->_count++;
    }

    public function inspect($depth = 0)
    {
        echo str_repeat("  ", $depth) . $this->repr() . PHP_EOL;
        foreach ($this->_children as $v) {
            $v->inspect($depth + 1);
        }
    }

    public function repr()
    {
        if ($this->isRoot()) {
            return sprintf("<%s (root)>", get_class($this));
        }

        return sprintf("<%s %s (%s)>", ' ', $this->_item, $this->_count);
    }
}
