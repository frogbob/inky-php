<?php

namespace Frogbob\InkyPHP\Component;

use Frogbob\InkyPHP\InkyPHP;
use PHPHtmlParser\Dom\HtmlNode;

class ContainerFactory extends AbstractComponentFactory
{

    const NAME = 'container';

    public function getName()
    {
        return self::NAME;
    }
    /**
     * <container>{inner}</container>
     * ----------------------------------
     * <table class="container {class}">
     *  <tbody>
     *      <tr>
     *          <td>{inner}</td>
     *      </tr>
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
        $this->addCssClass('container', $table);
        $table->setAttribute('align', 'center');

        $tbody = $this->tbody();
        $tr = $this->tr();
        $td = $this->td();
        $this->copyChildren($element, $td);

        $tr->addChild($td);
        $tbody->addChild($tr);
        $table->addChild($tbody);
        return $table;
    }


}
