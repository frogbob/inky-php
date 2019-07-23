<?php

namespace Frogbob\InkyPHP\Component;

use Frogbob\InkyPHP\InkyPHP;
use PHPHtmlParser\Dom\HtmlNode;

class MenuFactory extends AbstractComponentFactory
{

    const NAME = 'menu';

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * <menu>{inner}</menu>
     * ----------------------------
     * <table class="menu">
     *      <tr>
     *          <td>
     *              <table>
     *                  <tr>
     *                      {inner}
     *                  </tr>
     *              </table>
     *          </td>
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
        $table = $this->table($element->getAttributes());
        $this->addCssClass('menu', $table);
        $tr = $this->tr();
        $td = $this->td();
        $childTable = $this->table();
        $childTr = $this->tr();
        $this->copyChildren($element, $childTr);

        $childTable->addChild($childTr);
        $td->addChild($childTable);
        $tr->addChild($td);
        $table->addChild($tr);

        return $table;
    }


}
