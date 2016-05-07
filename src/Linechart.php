<?php

namespace Ckr\Linecharts;

/**
 * Contains the data of the whole image (i.e.. all data sets and configuration)
 */
class Linechart
{

    /**
     * @var Dataset[]
     */
    protected $dataSets = [];

    public function addDataSet(Dataset $dataset)
    {
        $this->dataSets[$dataset->getName()] = $dataset;
    }

    /**
     * Returns min and max values in x and y axis over all points (of all data sets)
     *
     * @return array[]
     */
    public function getTotalRange()
    {
        $xMin = $xMax = $yMin = $yMax = null;
        foreach ($this->dataSets as $set) {
            foreach ($set->getPoints() as $pt) {
                list($x, $y) = $pt;
                $xMin = null === $xMin ? $x : min($xMin, $x);
                $yMin = null === $yMin ? $y : min($yMin, $y);
                $xMax = null === $xMax ? $x : max($xMax, $x);
                $yMax = null === $yMax ? $y : max($yMax, $y);
            }
        }
        return [[$xMin, $xMax], [$yMin, $yMax]];
    }

    /**
     * @return Dataset[]
     */
    public function getDatasets()
    {
        return $this->dataSets;
    }
}
