<?php

namespace Ckr\Linecharts\Renderer;

/**
 * Helper data class for the rendere
 * NOT part of the public api
 */
class ImageGeom
{

    private $width;

    private $height;

    private $marginLeft;

    private $marginRight;

    private $marginTop;

    private $marginBottom;

    private $dataWidth;

    private $dataHeight;

    /**
     * @param int $width Total image width
     * @param int $height Total image height
     * @param int $marginLeft
     * @param int $marginRight
     * @param int $marginTop
     * @param int $marginBottom
     */
    public function __construct($width, $height, $marginLeft, $marginRight, $marginTop, $marginBottom)
    {
        $this->width = $width;
        $this->height = $height;
        $this->marginLeft = $marginLeft;
        $this->marginRight = $marginRight;
        $this->marginTop = $marginTop;
        $this->marginBottom = $marginBottom;

        $this->dataWidth = $width - $marginLeft - $marginRight;
        $this->dataHeight = $height - $marginTop - $marginBottom;

        if ($this->dataWidth <= 0) {
            throw new \LogicException('remaining width is (<=0) after subtracting margins');
        }
        if ($this->dataHeight <= 0) {
            throw new \LogicException('remaining height is (<=0) after subtracting margins');
        }
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getMarginLeft()
    {
        return $this->marginLeft;
    }

    /**
     * @return int
     */
    public function getMarginRight()
    {
        return $this->marginRight;
    }

    /**
     * @return int
     */
    public function getMarginTop()
    {
        return $this->marginTop;
    }

    /**
     * @return int
     */
    public function getMarginBottom()
    {
        return $this->marginBottom;
    }

    /**
     * @return int
     */
    public function getDataWidth()
    {
        return $this->dataWidth;
    }

    /**
     * @return int
     */
    public function getDataHeight()
    {
        return $this->dataHeight;
    }
}
