<?php

namespace Frogbob\InkyPHP\Component;

use Frogbob\InkyPHP\InkyPHP;
use PHPHtmlParser\Dom\HtmlNode;

class BlockGridFactory extends AbstractComponentFactory
{
    const NAME = 'block-grid';

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * <block-grid up="{up}">{inner}</block-grid>
     * ------------------------------------------
     * <table class="block-grid up-{up}">
     *  <tr>{inner}</tr>
     * </table>
     *
     * @param HtmlNode $element
     * @param InkyPHP $inkyInstance
     *
     * @return HtmlNode
     */
    public function parse(HtmlNode $element, InkyPHP $inkyInstance)
    {
        $upAttribute = (string) $element->getAttribute('up');
        $table = $this->table(array('class' => trim(sprintf(
            'block-grid up-%s %s',
            $upAttribute,
            (string) $element->getAttribute('class')
        ))));
        $tr = $this->tr();
        $this->copyChildren($element, $tr);
        $table->addChild($tr);

        return $table;
    }


}
