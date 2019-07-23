<?php

namespace Frogbob\InkyPHP\Component;

use Frogbob\InkyPHP\InkyPHP;
use PHPHtmlParser\Dom\HtmlNode;

class RowFactory extends AbstractComponentFactory
{

    const NAME = 'row';

    public function getName()
    {
        return self::NAME;
    }

    /**
     * <row>{inner}</row>
     * ---------------------------
     * <table class="row {class}">
     *  <tbody>
     *      <tr>{inner}</tr>
     *  </tbody>
     * </table>
     *
     * @param HtmlNode $element
     * @param Inky $inkyInstance
     *
     * @return HtmlNode
     */
    public function parse(HtmlNode $element, Inky $inkyInstance)
    {
        $table = $this->table($element->getAttributes());
        $this->addCssClass('row', $table);
        $body = $this->tbody();
        $tr = $this->tr();
        $body->addChild($tr);
        $table->addChild($body);
        $this->copyChildren($element, $tr);
        return $table;
    }


}
