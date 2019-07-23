<?php

namespace Frogbob\InkyPHP\Component;

use Frogbob\InkyPHP\InkyPHP;
use PHPHtmlParser\Dom\HtmlNode;

class WrapperFactory extends AbstractComponentFactory
{

    const NAME = 'wrapper';

    public function getName()
    {
        return self::NAME;
    }

    /**
     * <wrapper>{inner}</wrapper>
     * ---------------------------
     * <table class="wrapper" align="center">
     *    <tr>
     *      <td class="wrapper-inner">{inner}</td>
     *    </tr>
     * </table>
     *
     * @param HtmlNode $element
     * @param InkyPHP $inkyInstance
     *
     * @return HtmlNode
     */
    public function parse(HtmlNode $element, InkyPHP $inkyInstance)
    {
        $table = $this->table($element->getAttributes());
        $this->addCssClass('wrapper', $table);
        $table->setAttribute('align', 'center');
        $tr = $this->tr();
        $td = $this->td(['class' => 'wrapper-inner']);
        $this->copyChildren($element, $td);

        $tr->addChild($td);
        $table->addChild($tr);
        return $table;
    }


}
