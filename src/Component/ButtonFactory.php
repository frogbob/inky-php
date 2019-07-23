<?php

namespace Frogbob\InkyPHP\Component;

use Frogbob\InkyPHP\InkyPHP;
use PHPHtmlParser\Dom\HtmlNode;

class ButtonFactory extends AbstractComponentFactory
{
    const NAME = 'button';

    public function getName()
    {
        return self::NAME;
    }


    /**
     * <button href="" class="{class}">{inner}</button>
     * -----------------------------------------------
     * <table class="button {class}">
     *  <tr>
     *      <td>
     *          <table>
     *              <tr>
     *                  <td>{inner}</td>
     *              </tr>
     *          </table>
     *      </td>
     *  </tr>
     * </table>
     *
     * - OR -
     *
     * <button href="" class="expand {class}">{inner}</inner>
     * -----------------------------------------------
     * <table class="button {class}">
     *  <tr>
     *      <td>
     *          <table>
     *              <tr>
     *                  <center>
     *                      <td>{inner}</td>
     *                  </center>
     *              </tr>
     *          </table>
     *      </td>
     *      <td class="exapnder"></td>
     *  </tr>
     * </table>
     *
     * @param HtmlNode $element
     * @param Inky $inkyInstance
     *
     * @return HtmlNode
     */
    public function parse(HtmlNode $element, Inky $inkyInstance)
    {
        $attributes = $element->getAttributes();
        if(isset($attributes['href'])) {
            $href = $attributes['href'];
            unset($attributes['href']);
        } else {
            $href = null;
        }

        if(isset($attributes['target'])) {
            $target = $attributes['target'];
            unset($attributes['target']);
        } else {
            $target = null;
        }

        $table = $this->table($attributes);
        $this->addCssClass('button', $table);
        $tr = $this->tr();
        $td = $this->td();
        $childTable = $this->table();
        $childTr = $this->tr();
        $childTd = $this->td();

        $lastChild = $childTd;
        //wrap in center if element has class expand
        if($this->elementHasCssClass($element, 'expand')) {
            $center = $this->node('center');
            $lastChild->addChild($center);
            $lastChild = $center;
        }
        //wrap in <a /> if element has href
        if($href !== null) {
            $a = $this->node('a', array('href' => (string) $href));

            if($target !== null) {
                $a->setAttribute('target', (string) $target);
            }

            $lastChild->addChild($a);
            $lastChild = $a;
        }

        $this->copyChildren($element, $lastChild);

        $childTr->addChild($childTd);


        $childTable->addChild($childTr);
        $td->addChild($childTable);
        $tr->addChild($td);
        if($this->elementHasCssClass($element, 'expand')) {
            $expanderTd = $this->td(['class' => 'expander']);
            $tr->addChild($expanderTd);
        }
        $table->addChild($tr);

        return $table;
    }


}
