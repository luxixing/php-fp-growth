<?php
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

    public function getParent()
    {
        return $this->_parent;
    }

    public function setParent($value)
    {
        if ($value !== null && (!$value instanceof FPNode)) {
            throw new \Exception('A node must have an FPNode as a parent.');
        }
        if ($value && $value->getTree() !== $this->_tree) {
            throw new \Exception('Cannot have a parent from another tree.');

        }
        $this->_parent = $value;
    }

    public function getNeighbor()
    {
        return $this->_neighbor;
    }

    public function setNeighbor($value)
    {
        if ($value !== null && (!$value instanceof FPNode)) {

            throw new \Exception('A node must have an FPNode as a neighbor.');
        }
        if ($value && $value->getTree() !== $this->_tree) {

            throw new \Exception('Cannot have a neighbor from another tree.');
        }
        $this->_neighbor = $value;
    }

    public function getChildren()
    {
        return $this->_children;
    }

    public function isRoot()
    {
        return empty($this->_item) && empty($this->_count);
    }

    public function isLeaf()
    {
        return empty($this->_children);
    }

    public function isContain($item)
    {
        return in_array($item, $this->_children);
    }

    public function add($child)
    {
        if (!$child instanceof FPNode) {
            throw new \Exception('Can only add other FPNodes as children');
        }
        $item = $child->getItem();
        if (!in_array($item, $this->_children)) {
            $this->_children[$item] = $child;
            $child->setParent($child);
        }
    }

    public function search($item)
    {
        return isset($this->_children[$item]) ? $this->_children[$item] : null;
    }

    public function increment()
    {
        if ($this->_children === null) {
            throw new \Exception('Root nodes have no associated count.');
        }
        $this->_count++;
    }

    public function inspect($depth = 0)
    {
        echo str_repeat(' ', $depth) . $this->repr();
        foreach ($this->_children as $v) {
            $v->insect($depth + 1);
        }
    }

    public function repr()
    {
        if ($this->isRoot()) {
            return sprintf("<%s (root)>", get_class($this));
        }

        return sprintf("<%s %s (%s)>", get_class($this), $this->_item, $this->_count);
    }
}
