<?php

namespace Frogbob\InkyPHP\Component;

use Frogbob\InkyPHP\InkyPHP;
use PHPHtmlParser\Dom\Collection;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;

class SpacerFactory extends AbstractComponentFactory
{

    const NAME = 'spacer';

    public function getName()
    {
        return self::NAME;
    }

    /**
     * <spacer size="{size}" />
     * ---------------------------
     * <table class="spacer">
     *    <tbody>
     *      <tr>
     *          <td height="{size}px" style="font-size:{size}px;line-height:{size}px;">&#xA0;</td>
     *      </tr>
     *    </tbody>
     * </table>
     *
     * @param HtmlNode $element
     * @param Inky $inkyInstance
     *
     * @return HtmlNode
     */
    public function parse(HtmlNode $element, Inky $inkyInstance)
    {
        if($element->getAttribute('size-sm') || $element->getAttribute('size-lg'))
        {
            return $this->parseResponsive($element, $inkyInstance);
        }

        return $this->parseNormal($element, $inkyInstance);
    }

    /**
     * <spacer size="{size}" />
     * ---------------------------
     * <table class="spacer">
     *    <tbody>
     *      <tr>
     *          <td height="{size}px" style="font-size:{size}px;line-height:{size}px;">&#xA0;</td>
     *      </tr>
     *    </tbody>
     * </table>
     *
     * @param HtmlNode $element
     * @param Inky $inkyInstance
     *
     * @return HtmlNode
     */
    protected function parseNormal(HtmlNode $element, Inky $inkyInstance)
    {
        $size = $element->getAttribute('size') ? (int) $element->getAttribute('size') : 16;
        $table = $this->table($this->getUsableAttributes($element));
        $this->addCssClass('spacer', $table);
        $body = $this->tbody();
        $tr = $this->tr();
        $td = $this->td();

        $td->setAttribute('height', sprintf('%dpx', $size));
        $td->setAttribute('style', sprintf('font-size:%dpx;line-height:%dpx;', $size, $size));
        $td->addChild(new TextNode('&#xA0;'));

        $tr->addChild($td);
        $body->addChild($tr);
        $table->addChild($body);

        return $table;
    }

    /**
     * <spacer size-sm="{size-sm}" size-lg="{size-lg}" />
     * ---------------------------
     * <table class="spacer hide-for-large">
     *    <tbody>
     *      <tr>
     *          <td height="{size-sm}px" style="font-size:{size-sm}px;line-height:{size-sm}px;">&#xA0;</td>
     *      </tr>
     *    </tbody>
     * </table>
     * <table class="spacer show-for-large">
     *    <tbody>
     *      <tr>
     *          <td height="{size-lg}px" style="font-size:{size-lg}px;line-height:{size-lg}px;">&#xA0;</td>
     *      </tr>
     *    </tbody>
     * </table>
     *
     * @param HtmlNode $element
     * @param Inky $inkyInstance
     *
     * @return HtmlNode
     */
    protected function parseResponsive(HtmlNode $element, Inky $inkyInstance)
    {
        $sizes = [];
        if($element->getAttribute('size-sm'))
        {
            $sizes[] = 'size-sm';
        }

        if($element->getAttribute('size-lg'))
        {
            $sizes[] = 'size-lg';
        }

        $tables = new Collection();

        foreach($sizes as $spacer)
        {
            $size = (int) $element->getAttribute($spacer);
            $table = $this->table($this->getUsableAttributes($element));

            $responsiveClass = $spacer == 'size-sm' ? 'hide-for-large' : 'show-for-large';

            $this->addCssClass('spacer '.$responsiveClass, $table);
            $body = $this->tbody();
            $tr = $this->tr();
            $td = $this->td();

            $td->setAttribute('height', sprintf('%dpx', $size));
            $td->setAttribute('style', sprintf('font-size:%dpx;line-height:%dpx;', $size, $size));
            $td->addChild(new TextNode('&#xA0;'));

            $tr->addChild($td);
            $body->addChild($tr);
            $table->addChild($body);

            $tables[] = $table;
        }

        return $tables;
    }

}
