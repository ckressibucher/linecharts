<?php

namespace Ckr\Linecharts\Renderer;

use Ckr\Linecharts\Dataset;
use Ckr\Linecharts\Grid;
use Ckr\Linecharts\Linechart;
use Ckr\Linecharts\Svg\Path;
use Ckr\Linecharts\Svg\Svg;

class SvgRenderer
{

    /**
     * The main entry point to render a svg image.
     *
     * The main data source is the given `Linechart`.
     *
     * @param Linechart $linechart Defines the datasets to render
     * @param int $width           Target width
     * @param int $height          Target height
     * @param Grid $grid           The grid
     * @param int $precision       Precision used to round values
     * @param string|bool $inlineStyle Css
     * @param array|null $margins  Top, Bottom, Left, Right margin (or null to set all to 0)
     * @return string
     */
    public function render(
        Linechart $linechart,
        $width,
        $height,
        Grid $grid = null,
        $precision = 3,
        $inlineStyle = true,
        $margin = null
    ) {
        $svg = new Svg($width, $height, $inlineStyle);
        $margin = $margin ?: [0, 0, 0, 0];
        list($top, $bottom, $left, $right) = $margin;

        $imageGeom = new ImageGeom($width, $height, $left, $right, $top, $bottom);
        $paths = $this->makePaths($linechart, $imageGeom, $precision);
        $svg->addCollection($paths);
        if ($grid) {
            $gridPaths = $this->makeGridElements($linechart, $grid, $imageGeom, $precision);
            $svg->addCollection($gridPaths);
        }
        return $svg->render();
    }

    protected function flipYCoordinates(array $points, $height)
    {
        return array_map(function (array $pt) use ($height) {
            list($x, $y) = $pt;
            return [
                $x,
                -1 * $y + $height, // mirror y coordinate system
            ];
        }, $points);
    }

    /**
     * @param array $points (x,y) points in svg coordinates (normalized and flipped)
     * @param int $marginLeft
     * @param int $marginTop
     * @return array
     */
    protected function transformByMargin(array $points, $marginLeft, $marginTop)
    {
        return array_map(function ($pt) use ($marginLeft, $marginTop) {
            list($x, $y) = $pt;
            return [
                $x + $marginLeft,
                $y + $marginTop,
            ];
        }, $points);
    }

    /**
     * Normalize points inside x-/y-range to target coordinates
     *
     * @param array $points
     * @param int $width Target width
     * @param int $height Target height
     * @param array $xRange min/max values in x direction
     * @param array $yRange min/max values in y direction
     * @return array
     */
    protected function normalizePoints(array $points, $width, $height, array $xRange, array $yRange)
    {
        list($xMin, $xMax) = $xRange;
        list($yMin, $yMax) = $yRange;
        $xRatio = ($xMax - $xMin) / $width;
        $yRatio = ($yMax - $yMin) / $height;

        $normalizedDataSet = array_map(function ($pt) use ($xRatio, $yRatio, $xMin, $yMin) {
            list($x, $y) = $pt;
            return [
                0 == $xRatio ? 0 : ($x - $xMin) / $xRatio,
                0 == $yRatio ? 0 : ($y - $yMin) / $yRatio,
            ];
        }, $points);
        return $normalizedDataSet;
    }

    protected function makePaths(Linechart $linechart, ImageGeom $geom, $precision)
    {
        $sets = $linechart->getDatasets();
        list($xRange, $yRange) = $linechart->getTotalRange();
        $cnt = 1;
        $paths = [];
        foreach ($sets as $dataset) {
            $paths[] = $this->createPath($dataset, $geom, $xRange, $yRange, $precision, $cnt);
            $cnt++;
        }
        return $paths;
    }

    protected function makeGridElements(Linechart $linechart, Grid $grid, ImageGeom $geom, $precision)
    {
        list($xRange, $yRange) = $linechart->getTotalRange();
        list($xMin, $xMax) = $xRange;
        list($yMin, $yMax) = $yRange;

        $fnMakeLines = function ($numOfLines, $max, $min) {
            if ($numOfLines <= 0) {
                return [];
            }
            if (1 === $numOfLines) {
                return [$min];
            }
            $lines = [];
            $sections = $numOfLines - 1; // the area between two lines
            $diff = $max - $min;
            $interval = $diff / $sections;
            for ($i = 0; $i <= $sections; $i++) {
                $lines[] = $i === $sections ? $max : $min + $i * $interval;
            }
            return $lines;
        };
        // calculate y values of horizontal lines
        $yLines = $fnMakeLines($grid->getNumHorizontal(), $yMax, $yMin);

        // x values of vertical lines
        $paths = [];
        $xLines = $fnMakeLines($grid->getNumVertical(), $xMax, $xMin);
        $cssY = ['gridline', 'horizontal'];
        foreach ($yLines as $y) {
            $pts = [[$xMin, $y], [$xMax, $y]];
            $paths[] = $this->createPathFromPoints($pts, $cssY, $geom, $xRange, $yRange, $precision);
        }
        $cssX = ['gridline', 'vertical'];
        foreach ($xLines as $x) {
            $pts = [[$x, $yMin], [$x, $yMax]];
            $paths[] = $this->createPathFromPoints($pts, $cssX, $geom, $xRange, $yRange, $precision);
        }
        return $paths;
    }

    protected function createPath(Dataset $dataset, ImageGeom $geom, $xRange, $yRange, $precision, $cnt)
    {
        $points = $dataset->getPoints();
        $cssClasses = $this->getCssClasses($dataset, $cnt);
        return $this->createPathFromPoints($points, $cssClasses, $geom, $xRange, $yRange, $precision);
    }

    protected function createPathFromPoints(array $points, $cssClasses, ImageGeom $geom, $xRange, $yRange, $precision)
    {
        $points = $this->normalizePoints($points, $geom->getDataWidth(), $geom->getDataHeight(), $xRange, $yRange);
        $points = $this->flipYCoordinates($points, $geom->getDataHeight());
        $points = $this->transformByMargin($points, $geom->getMarginLeft(), $geom->getMarginTop());
        return new Path($points, implode(' ', $cssClasses), $precision);
    }

    protected function getCssClasses(Dataset $dataset, $cnt)
    {
        $classes = [
            'line',
            'dataset-' . strval($cnt),
            'dataset-' . $this->getSlug($dataset->getName()),
        ];
        if ($dataset->getCssClass()) {
            $classes[] = $dataset->getCssClass();
        }
        return $classes;
    }

    protected function getSlug($name)
    {
        return preg_replace('/[^a-zA-Z0-0]/', '-', $name);
    }
}
