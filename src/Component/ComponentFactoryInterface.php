<?php

namespace Frogbob\InkyPHP\Component;

use Frogbob\InkyPHP\InkyPHP;
use PHPHtmlParser\Dom\AbstractNode;
use PHPHtmlParser\Dom\Collection;
use PHPHtmlParser\Dom\HtmlNode;

interface ComponentFactoryInterface
{

    /**
     * @return string
     */
    public function getName();

    /**
     * @param HtmlNode $element
     * @param InkyPHP $inkyInstance
     *
     * @return AbstractNode|Collection
     */
    public function parse(HtmlNode $element, InkyPHP $inkyInstance);

}
