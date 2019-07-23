<?php

namespace Frogbob\InkyPHP\Component;

use Frogbob\InkyPHP\InkyPHP;
use PHPHtmlParser\Dom\HtmlNode;

class CalloutFactory extends AbstractComponentFactory
{

    const NAME = 'callout';

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * <callout>{inner}</callout>
     * ----------------------------------
     *  <table class="callout">
     *      <tr>
     *          <th class="callout-inner">Callout</th>
     *          <th class="expander"></th>
     *      </tr>
     *  </table>
     *
     * @param HtmlNode $element
     * @param InkyPHP $inkyInstance
     *
     * @return HtmlNode
     */
    public function parse(HtmlNode $element, InkyPHP $inkyInstance)
    {
        $table = $this->table($this->getUsableAttributes($element, 'class'));
        $this->addCssClass('callout', $table);
        $tr = $this->tr();

        $th = $this->th($element->getAttributes());
        $this->addCssClass('callout-inner', $th);
        $this->copyChildren($element, $th);
        $tr->addChild($th);
        $expander = $this->th(['class' => 'expander']);
        $tr->addChild($expander);
        $table->addChild($tr);
        return $table;
    }


}
