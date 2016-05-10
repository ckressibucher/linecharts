<?php

namespace Ckr\Linecharts;

/**
 * Represents a grid for the linechart
 */
class Grid
{

    /**
     * @var int
     */
    protected $numHorizontal;

    /**
     * @var int
     */
    protected $numVertical;

    /**
     * @param int $numHorizontal
     * @param int $numVertical
     */
    public function __construct($numHorizontal, $numVertical)
    {
        $this->numHorizontal = (int) $numHorizontal;
        $this->numVertical = (int) $numVertical;
    }

    /**
     * @return int
     */
    public function getNumHorizontal()
    {
        return $this->numHorizontal;
    }

    /**
     * @return int
     */
    public function getNumVertical()
    {
        return $this->numVertical;
    }
}
