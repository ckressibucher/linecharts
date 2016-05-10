<?php

use Ckr\Linecharts\Renderer\SvgRenderer;

class SvgRendererTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SvgRenderer
     */
    private $renderer;

    protected function setUp()
    {
        $this->renderer = new SvgRenderer();
    }

    /**
     * @test
     */
    public function it_renders_one_horizontal_line_on_bottom()
    {
        $w = $h = 100;
        $lineChart = $this->createDefaultLineChart($w, $h);
        $grid = new \Ckr\Linecharts\Grid(1, 0); // one horizonal line

        $xmlString = $this->renderer->render($lineChart, $w, $h, $grid);

        $asXml = new SimpleXMLElement($xmlString);
        $asXml->registerXPathNamespace('def', 'http://www.w3.org/2000/svg');
        $xpath = '/def:svg/def:path[@class="gridline horizontal"]/@d';
        $expected = 'M0,100L100,100';
        $this->assertXPathAttributeSame($asXml, $xpath, $expected);
    }

    /**
     * @test
     */
    public function it_renders_two_horizontal_lines_on_bottom_and_top()
    {
        $w = $h = 100;
        $lineChart = $this->createDefaultLineChart($w, $h);
        $grid = new \Ckr\Linecharts\Grid(2, 0); // two horizonal line

        $xmlString = $this->renderer->render($lineChart, $w, $h, $grid);

        $asXml = new SimpleXMLElement($xmlString);
        $asXml->registerXPathNamespace('def', 'http://www.w3.org/2000/svg');
        $xpath = '/def:svg/def:path[@class="gridline horizontal"]/@d';
        $result = $asXml->xpath($xpath);
        $result = array_map('strval', $result);
        sort($result);
        $this->assertCount(2, $result);
        list($top, $bottom) = $result;
        $this->assertSame('M0,0L100,0', $top);
        $this->assertSame('M0,100L100,100', $bottom);
    }

    /**
     * @test
     */
    public function it_renders_three_horizontal_lines()
    {
        $w = $h = 100;
        $lineChart = $this->createDefaultLineChart($w, $h);
        $grid = new \Ckr\Linecharts\Grid(3, 0); // three horizonal line

        $xmlString = $this->renderer->render($lineChart, $w, $h, $grid);

        $asXml = new SimpleXMLElement($xmlString);
        $asXml->registerXPathNamespace('def', 'http://www.w3.org/2000/svg');
        $xpath = '/def:svg/def:path[@class="gridline horizontal"]/@d';
        $result = $asXml->xpath($xpath);
        $result = array_map('strval', $result);
        sort($result); // sort alphabetically
        $this->assertCount(3, $result);
        list($top, $bottom, $middle) = $result;
        $this->assertSame('M0,0L100,0', $top);
        $this->assertSame('M0,50L100,50', $middle);
        $this->assertSame('M0,100L100,100', $bottom);
    }

    /**
     * @test
     */
    public function it_renders_one_vertical_line_on_left_side()
    {
        $w = $h = 100;
        $lineChart = $this->createDefaultLineChart($w, $h);
        $grid = new \Ckr\Linecharts\Grid(0, 1);

        $xmlString = $this->renderer->render($lineChart, $w, $h, $grid);

        $asXml = new SimpleXMLElement($xmlString);
        $asXml->registerXPathNamespace('def', 'http://www.w3.org/2000/svg');
        $xpath = '/def:svg/def:path[@class="gridline vertical"]/@d';
        $expected = 'M0,100L0,0';
        $this->assertXPathAttributeSame($asXml, $xpath, $expected);
    }

    /**
     * @test
     */
    public function it_renders_no_grid_when_null_was_given()
    {
        $lineChart = $this->createDefaultLineChart(100, 100);
        $grid = null;
        $xmlString = $this->renderer->render($lineChart, 100, 100, $grid);

        $asXml = new SimpleXMLElement($xmlString);
        $asXml->registerXPathNamespace('def', 'http://www.w3.org/2000/svg');
        $xpath = '/def:svg/def:path/@class';
        $paths = $asXml->xpath($xpath);
        $this->assertCount(1, $paths); // for the data set, no grid paths
        $asString = (string) reset($paths);
        $this->assertNotContains('gridline', $asString);
    }

    /**
     * @test
     */
    public function it_allows_to_define_margins()
    {
        // data set with a line from 0,0 .. 100,100 (left bottom to right top)
        $dataSet = new \Ckr\Linecharts\Dataset([[0, 0], [100, 100]], 'data');
        $lineChart = new \Ckr\Linecharts\Linechart();
        $lineChart->addDataSet($dataSet);

        $w = $h = 120; // total image width/height
        $margin = [5, 15, 2, 18]; // top / bottom / left / right
        $xmlString = $this->renderer->render($lineChart, $w, $h, null, 3, true, $margin);

        $asXml = new SimpleXMLElement($xmlString);
        $asXml->registerXPathNamespace('def', 'http://www.w3.org/2000/svg');

        $xpathPath = '/def:svg/def:path/@d'; // xpath to the 'd' attribute of the data set path
        $expected = 'M2,105L102,5';
        $this->assertXPathAttributeSame($asXml, $xpathPath, $expected);
    }

    /**
     * @test
     */
    public function it_transforms_grid_coordinates_by_margin()
    {
        // data set with a line from 0,0 .. 100,100 (left bottom to right top)
        $dataSet = new \Ckr\Linecharts\Dataset([[0, 0], [100, 100]], 'data');
        $lineChart = new \Ckr\Linecharts\Linechart();
        $lineChart->addDataSet($dataSet);

        $grid = new \Ckr\Linecharts\Grid(2, 2);

        $w = $h = 120; // total image width/height
        $margin = [5, 15, 2, 18]; // top / bottom / left / right
        $xmlString = $this->renderer->render($lineChart, $w, $h, $grid, 3, true, $margin);

        $asXml = new SimpleXMLElement($xmlString);
        $asXml->registerXPathNamespace('def', 'http://www.w3.org/2000/svg');

        $xpathHoriz = '/def:svg/def:path[@class="gridline horizontal"]/@d';
        $xpathVertical = '/def:svg/def:path[@class="gridline vertical"]/@d';

        $dataHoriz = array_map('strval', $asXml->xpath($xpathHoriz));
        $dataVertical = array_map('strval', $asXml->xpath($xpathVertical));

        $this->assertCount(2, $dataHoriz);
        $this->assertCount(2, $dataVertical);

        $expectedHoriz = [
            'M2,105L102,105',
            'M2,5L102,5',
        ];
        $expectedVertical = [
            'M2,105L2,5',
            'M102,105L102,5',
        ];
        foreach ($expectedHoriz as $exp) {
            $this->assertContains($exp, $dataHoriz);
        }
        foreach ($expectedVertical as $exp) {
            $this->assertContains($exp, $dataVertical);
        }
    }

    /**
     * Extracts a node / attribute / .. from the given xml, converts it to a string,
     * and compared against the expected value.
     *
     * The given xpath must match exactly one xml node
     *
     * @param SimpleXMLElement $xml
     * @param $xpath
     * @param $expected
     */
    private function assertXPathAttributeSame(SimpleXMLElement $xml, $xpath, $expected)
    {
        $nodes = $xml->xpath($xpath);
        $this->assertCount(1, $nodes);
        $pathData = (string) reset($nodes);
        $this->assertSame($expected, $pathData);
    }

    /**
     * @return \Ckr\Linecharts\Linechart
     */
    private function createDefaultLineChart($width, $height)
    {
        // dataset to defined the data range of size 100 x 100
        $dataSet = new \Ckr\Linecharts\Dataset([[0, 0], [$width, $height]], 'data');
        $lineChart = new \Ckr\Linecharts\Linechart();
        $lineChart->addDataSet($dataSet);
        return $lineChart;
    }
}
