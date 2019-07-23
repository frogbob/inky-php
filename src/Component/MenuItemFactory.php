<?php

namespace Frogbob\InkyPHP\Component;

use Frogbob\InkyPHP\InkyPHP;
use PHPHtmlParser\Dom\HtmlNode;

class MenuItemFactory extends AbstractComponentFactory
{

    const NAME = 'item';

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * <item href="{href}">{inner}</item>
     * ------------------------------
     * <th class="menu-item">
     *  <a href="{href}">{inner}</a>
     * </th>
     *
     * @param HtmlNode $element
     * @param InkyPHP $inkyInstance
     *
     * @return HtmlNode
     */
    public function parse(HtmlNode $element, InkyPHP $inkyInstance)
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

        $th = $this->th($attributes);
        $this->addCssClass('menu-item', $th);
        $a = $this->node('a');
        if($href !== null) {
            $a->setAttribute('href', $href);

            if($target !== null) {
                $a->setAttribute('target', (string) $target);
            }
        }
        $this->copyChildren($element, $a);
        $th->addChild($a);

        return $th;
    }

}
