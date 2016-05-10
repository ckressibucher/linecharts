<?php

use Ckr\Linecharts\Renderer\ImageGeom;

class ImageGeomTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function it_stores_constructor_arguments()
    {
        // image width / height
        $w = 200;
        $h = 100;

        // margins (left, right, top, bottom)
        $ml = 10;
        $mr = 20;
        $mt = 30;
        $mb = 40;
        $geom = new ImageGeom($w, $h, $ml, $mr, $mt, $mb);

        $this->assertSame($w, $geom->getWidth());
        $this->assertSame($h, $geom->getHeight());
        $this->assertSame($ml, $geom->getMarginLeft());
        $this->assertSame($mr, $geom->getMarginRight());
        $this->assertSame($mt, $geom->getMarginTop());
        $this->assertSame($mb, $geom->getMarginBottom());
    }

    /**
     * @test
     */
    public function it_calculates_data_width_as_totalWidth_minus_margins()
    {
        $width = 100;
        $marginLeft = 10;
        $marginRight = 20;
        $geom = new ImageGeom($width, 10, $marginLeft, $marginRight, 0, 0);
        $this->assertSame(70, $geom->getDataWidth());
    }

    /**
     * @test
     */
    public function it_calculates_data_height_as_totalHeight_minus_margins()
    {
        $height = 200;
        $marginTop = 10;
        $marginBottom = 40;
        $geom = new ImageGeom(100, $height, 0, 0, $marginTop, $marginBottom);
        $this->assertSame(150, $geom->getDataHeight());
    }

    /**
     * @test
     */
    public function it_throws_LogicException_if_data_width_smallerEqual_zero()
    {
        $width = 30;
        $marginLeft = 10;
        $marginRight = 20;
        $this->setExpectedException(LogicException::class);
        new ImageGeom($width, 10, $marginLeft, $marginRight, 0, 0);
    }

    /**
     * @test
     */
    public function it_throws_LogicException_if_data_height_is_smallerEqual_zero()
    {
        $height = 30;
        $marginTop = 10;
        $marginBottom = 40;
        $this->setExpectedException(LogicException::class);
        new ImageGeom(100, $height, 0, 0, $marginTop, $marginBottom);
    }
}
