<?php

namespace Frogbob\InkyPHP\Component;

use Frogbob\InkyPHP\InkyPHP;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;

class RawFactory extends AbstractComponentFactory
{

    const NAME = 'raw';

    public function getName()
    {
        return self::NAME;
    }

    /**
     * <raw><%= test %></raw>
     * ---------------------------
     * <%= test %>
     *
     * @param HtmlNode $element
     * @param InkyPHP $inkyInstance
     *
     * @return TextNode
     */
    public function parse(HtmlNode $element, InkyPHP $inkyInstance)
    {
        return new TextNode($element->innerHtml());
    }

}
