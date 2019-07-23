<?php

namespace Frogbob\InkyPHP\Component;


use Frogbob\InkyPHP\InkyPHP;
use PHPHtmlParser\Dom\HtmlNode;

class InkyFactory extends AbstractComponentFactory
{

    protected $inkySrc = 'https://raw.githubusercontent.com/arvida/emoji-cheat-sheet.com/master/public/graphics/emojis/octopus.png';

    const NAME = 'inky';

    public function getName()
    {
        return self::NAME;
    }

    /**
     * <inky />
     * ----------------------------
     * <tr>
     *  <td>
     *      <img src="{inkySrc}" />
     *  </td>
     * </tr>
     *
     * @param HtmlNode $element
     * @param Inky $inkyInstance
     *
     * @return HtmlNode
     */
    public function parse(HtmlNode $element, Inky $inkyInstance)
    {
        $tr = $this->tr();
        $td = $this->td();
        $img = $this->img(array('src' => $this->inkySrc));
        $td->addChild($img);
        $tr->addChild($td);

        return $tr;
    }


}
