<?php

use Ckr\Linecharts\Linechart;
use Ckr\Linecharts\Renderer\SvgRenderer;

class LinechartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider simpleLines
     */
    public function it_renders_basic_datasets_as_lines($pattern, $values)
    {
        $chart = new Linechart();
        $chart->addDataSet(new \Ckr\Linecharts\Dataset($values, 'data'));
        $renderer = new SvgRenderer();
        $this->assertContains($pattern, $renderer->render($chart, 100, 100));
    }

    public function simpleLines()
    {
        // The pattern assume that all data is rendered on 100x100 charts
        return [
            [
                '0,100L100,0', // expected pattern, note that [0,0] is top left in svg
                [[0, 0], [100, 100]],
            ], [
                '0,100L100,0',
                [[0, 0], [200, 100]],
            ],
            [
                '0,100L100,0',
                [[20, 0], [100, 100]],
            ],
            [
                '0,100L50,0,100,100',
                [[0, 0], [50, 100], [100, 0]],
            ],
            [
                '0,100L50,0,100,100',
                [[0, 0], [100, 300], [200, 0]],
            ],
            [ // only show the area which contains data
                '0,100L100,0',
                [[50, 0], [100, 100]],
            ],
        ];
    }

    /**
     * @test
     */
    public function it_adds_custom_css_class_to_path_element()
    {
        $chart = new Linechart();
        $chart->addDataSet(new \Ckr\Linecharts\Dataset([[0, 0], [100, 100]], 'data', 'my-custom-dataset-class'));
        $renderer = new SvgRenderer();
        $result = $renderer->render($chart, 100, 100);
        $this->assertRegExp('/\<path[^\>]+class=".*my-custom-dataset-class.*"/', $result);
    }

}
