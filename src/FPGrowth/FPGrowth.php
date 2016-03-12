<?php
namespace FPGrowth;

use FPGrowth\Base\FPNode;
use FPGrowth\Base\FPTree;

class  FPGrowth
{

    private function conditionalTreeFromPaths($paths)
    {
        $tree = new FPTree();
        $conditionItem = null;
        $items = [];
        foreach ($paths as $path) {
            if (empty($conditionItem)) {
                $p = $path[count($path) -1];
                $conditionItem = $p->getItem();
            }
            $point = $tree->getRoot();
            foreach ($path as $node) {
                $nextPoint = $node->search($node->getItem());
                if (empty($nextPoint)) {
                    $items[] = $node->getItem();
                    $count = $node->getItem() == $conditionItem ? $node->getCount() : 0;
                    $nextPoint = new FPNode($tree, $node->getItem(), $count);
                    $point->add($nextPoint);
                    $tree->updateRoute($nextPoint);
                }
                $point = $nextPoint;
            }
        }
        if (empty($conditionItem)) {
            throw  new \Exception('condition tree is wrong');
        }
        foreach ($tree->prefixPaths($conditionItem) as $path) {
            $p = array_pop($path);
            $count = $p->getCount();
            foreach (array_reverse($path) as $node) {
                $node->setCount($count);
            }
        }

        return $tree;
    }

    private function findWithSuffix($tree, $suffix, $minimum, $includeSupport)
    {
        foreach ($tree->getItems() as $item => $nodes) {
            $support = 0;
            foreach ($nodes as $v) {
                $support += $v->getCount();
            }
            if ($support >= $minimum && !isset($suffix[$item])) {
                $foundSet = [$item, $suffix];
                yield $includeSupport ? [$foundSet, $support] : $foundSet;
                $condTree = $this->conditionalTreeFromPaths($tree->prefixPaths($item));
                foreach ($this->findWithSuffix($condTree, $foundSet, $minimum, $includeSupport) as $v) {
                    yield $v;
                }

            }
        }
    }

    public function findFrequentItemSets($transactions, $minimum, $includeSupport = false)
    {
        $items = [];
        foreach ($transactions as $v) {
            foreach ($v as $v1) {
                $items[$v1] = isset($items[$v1]) ? $items[$v1] + 1 : 1;
            }
        }
        foreach ($items as $k => $v) {
            if ($v < $minimum) {
                unset($items[$k]);
            }
        }
        $cleanTransaction = function ($transaction) use ($items) {
            $ret = [];
            foreach ($transaction as $v) {
                if (isset($items[$v])) {
                    $ret[$v] = $items[$v];
                }
            }
            arsort($ret);

            return array_keys($ret);
        };
        $master = new FPTree();
        foreach ($transactions as $v) {
            $master->add($cleanTransaction($v));
        }
        $master->inspect();
        foreach ($this->findWithSuffix($master, [], $minimum, $includeSupport) as $v) {
            yield $v;
        }
    }
}
