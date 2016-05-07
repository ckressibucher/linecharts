<?php

namespace Ckr\Linecharts;

class Dataset
{

    /**
     * a set of points defined by [x,y] values
     *
     * @var array[]
     */
    protected $points;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $cssClass;

    /**
     * @param array $points
     * @param string $name
     */
    public function __construct(array $points, $name, $cssClass = null)
    {
        $this->points = $points;
        $this->name = $name;
        $this->cssClass = $cssClass;
    }

    /**
     * Get points, sorted to increase in x direction
     *
     * @return array[]
     */
    public function getPoints()
    {
        $points = $this->points;
        usort($points, function ($a, $b) {
            return $a[0] < $b[0] ? -1 : 1;
        });
        return $points;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getCssClass()
    {
        return $this->cssClass;
    }
}
