<?php

use Ckr\Linecharts\Svg\Path;

/**
 * @covers Ckr\Linecharts\Svg\Path
 */
class SvgPathTest extends \PHPUnit_Framework_TestCase
{

    private $data = [[0, 0], [1, 2], [3.12344, 1.12344], [100, 40]];

    /**
     * @test
     * @dataProvider pathProvider
     */
    public function it_renders_simple_path($data, $expected, $cssClass, $precision)
    {
        $path = new Path($data, $cssClass, $precision);
        $this->assertSame(
            $expected,
            $path->render()
        );
    }

    public function pathProvider()
    {
        return [
            [
                [[0, 0], [1, 2], [3.1234567, 1.1234567], [100, 40]],
                '<path class="path" d="M0,0L1,2,3.123,1.123,100,40"></path>',
                null,
                null,
            ],
            [
                $this->data,
                '<path class="path" d="M0,0L1,2,3.1234,1.1234,100,40"></path>',
                null,
                4,
            ],
            [
                $this->data,
                '<path class="something" d="M0,0L1,2,3.123,1.123,100,40"></path>',
                'something',
                null,
            ],
        ];
    }

    /**
     * @test
     */
    public function it_thrws_exception_if_less_than_two_points_defined()
    {
        $this->setExpectedException(\LogicException::class);
        new Path([[0,10]]);
    }

    /**
     * @test
     */
    public function it_throws_exception_if_precision_is_not_integer()
    {
        $this->setExpectedException(\InvalidArgumentException::class); 
        new Path([[0,0], [10,10]], null, 3.2);
    }

}
