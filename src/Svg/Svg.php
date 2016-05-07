<?php

namespace Ckr\Linecharts\Svg;

/**
 * TODO separate renderer class
 */
class Svg implements SvgElement
{

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var string|bool
     */
    protected $inlineStyle;

    /**
     * @var SvgElement[]
     */
    protected $children = [];

    /**
     * @param int $width
     * @param int $height
     * @param string|bool $inlineStyle Set to boolean value to include/not include
     *                                 default inline style. Set to a string to
     *                                 define a custom inline style
     */
    public function __construct($width, $height, $inlineStyle = false)
    {
        if (!is_string($inlineStyle) && !is_bool($inlineStyle)) {
            throw new \InvalidArgumentException('inlineSytle must be a bool or string value');
        }
        $this->width = (int) $width;
        $this->height = (int) $height;
        $this->inlineStyle = $inlineStyle;
    }

    public function add(SvgElement $item)
    {
        array_push($this->children, $item);
    }

    /**
     * @param SvgElement[] $childElements
     */
    public function addCollection(array $childElements)
    {
        foreach ($childElements as $element) {
            $this->add($element);
        }
    }

    public function render()
    {
        // Set default value if `$this::inlineStyle` has unsupported type.
        // However, this should not be the case as it is validated in constructor       
        $inlineStyle = ''; 
        if (true === $this->inlineStyle) {
            $inlineStyle = $this->getDefaultInlineStyle();
        } elseif (false === $this->inlineStyle) {
            $inlineStyle = '';
        } elseif (is_string($this->inlineStyle)) {
            // string: custom inline style
            $inlineStyle = sprintf('<style>%s</style>', $this->inlineStyle);
        }
        $components = '';
        foreach ($this->children as $child) {
            $components .= $child->render();
        }

        $head = '<?xml version="1.0" encoding="UTF-8"?>';
        return sprintf(
            '%s<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d">%s%s</svg>',
            $head,
            $this->width,
            $this->height,
            $inlineStyle,
            $components
        );
    }

    /**
     * @return string
     */
    protected function getDefaultInlineStyle()
    {
        $inlineStyle = '<style>
.line {
  fill: none;
  stroke: black;
  stroke-width: 1px;
}
.line.dataset-1 {
	stroke: #2222ff;
}
.line.dataset-2 {
	stroke: #00aa00;
}
.line.dataset-3 {
    stroke: #dd0011;
}
.line.dataset-4 {
    stroke: #bb9922;
}
</style>';
        return $inlineStyle;
    }
}
