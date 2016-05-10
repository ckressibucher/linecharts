<?php

namespace Ckr\Linecharts\Svg;

/**
 * @covers Ckr\Linecharts\Svg\Svg
 */
class SvgTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function it_renders_empty_svg_element()
    {
        $svg = new Svg(100, 100);
        $this->assertEquals(
            '<?xml version="1.0" encoding="UTF-8"?><svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"></svg>',
            $svg->render()
        );
    }

    /**
     * @test
     */
    public function it_uses_custom_inline_style()
    {
        $svg = new Svg(100, 100, '.line {fill: #cecece; }');
        $this->assertEquals(
            '<?xml version="1.0" encoding="UTF-8"?><svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><style>.line {fill: #cecece; }</style></svg>',
            $svg->render()
        );
    }

    /**
     * @test
     */
    public function it_renders_child_components()
    {
        $svg = new Svg(100, 100);
        $svg->add(new Path([[0, 0], [100, 0]]));
        $svg->add(new Path([[0, 100], [100, 100]], 'testing'));

        $this->assertEquals(
            '<?xml version="1.0" encoding="UTF-8"?><svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><path class="path" d="M0,0L100,0"></path><path class="testing" d="M0,100L100,100"></path></svg>',
            $svg->render()
        );
    }

    /**
     * @test
     */
    public function it_throws_exception_if_inline_style_is_of_wrong_type()
    {
        // should be boolean or string
        $inlineStyle = 5;
        $this->setExpectedException(\InvalidArgumentException::class);
        new Svg(100, 100, $inlineStyle);
    }
}
