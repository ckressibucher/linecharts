<?php

use Ckr\Linecharts\Renderer\SvgRenderer;
use Ckr\Linecharts\Linechart;
use Ckr\Linecharts\Dataset;
use Ckr\Linecharts\Grid;

include './vendor/autoload.php';

$seriesA = [[1, 3], [10, 30]]; // just  a single line
$seriesB = [[2, 30], [4, 34], [8, 22], [3, 12]];

$linechart = new Linechart();
$linechart->addDataSet(new Dataset($seriesA, 'num of users'));
$linechart->addDataSet(new Dataset($seriesB, 'num of empty bottles'));

$grid = new Grid(10, 1);
// or: 
// $grid = null;

$margins = [10, 30, 20, 20]; // top, bottom, left, right

$renderer = new SvgRenderer();
echo $renderer->render($linechart, 800, 600, $grid, 3, true, $margins);

