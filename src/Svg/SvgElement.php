<?php
namespace Ckr\Linecharts\Svg;

interface SvgElement
{

    /**
     * Returns an xml node
     *
     * @return string
     */
    public function render();
}
