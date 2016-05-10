<?php

class GridTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function it_stores_num_of_lines()
    {
        $grid = new \Ckr\Linecharts\Grid(5, 2);
        $this->assertSame(5, $grid->getNumHorizontal());
        $this->assertSame(2, $grid->getNumVertical());
    }
}
