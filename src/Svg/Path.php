<?php

namespace Ckr\Linecharts\Svg;

/**
 * TODO rendering should be done in a renderer class, not by the path element (data) itself
 */
class Path implements SvgElement
{

    /**
     * @var array
     */
    protected $pathPoints;

    /**
     * @var string
     */
    protected $cssClass;

    /**
     * @var int
     */
    protected $precision;

    /**
     * @param array $pathPoints
     * @param string $cssClass
     * @param int $precision
     */
    public function __construct(array $pathPoints, $cssClass = null, $precision = null)
    {
        $precision = null === $precision ? 3 : $precision;
        $cssClass = null === $cssClass ? 'path' : $cssClass;

        if (count($pathPoints) < 2) {
            throw new \LogicException('We must have at least two path points');
        }
        if (!is_int($precision)) {
            throw new \InvalidArgumentException('precision argument must be an integer');
        }
        $this->pathPoints = $pathPoints;
        $this->cssClass = $cssClass;
        $this->precision = $precision;
    }

    public function render()
    {
        $precision = $this->precision;

        $pathPoints = array_map(function ($pt) use ($precision) {
            return [
                is_int($pt[0]) ? $pt[0] : round($pt[0], $precision),
                is_int($pt[1]) ? $pt[1] : round($pt[1], $precision),
            ];
        }, $this->pathPoints);

        $startPoint = array_shift($pathPoints);
        $secondPoint = array_shift($pathPoints);
        $data = 'M' . implode(',', $startPoint) . 'L' . implode(',', $secondPoint);

        // add all other points, separated by comma
        $data = array_reduce($pathPoints, function ($carry, array $item) {
            return $carry . ',' . implode(',', $item);
        }, $data);

        return sprintf(
            '<path class="%s" d="%s"></path>',
            $this->cssClass,
            $data
        );
    }
}
