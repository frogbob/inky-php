<?php

namespace Frogbob\InkyPHP\Component;

use Frogbob\InkyPHP\InkyPHP;
use PHPHtmlParser\Dom\HtmlNode;

class ColumnsFactory extends AbstractComponentFactory
{
    const NAME = 'columns';

    protected $gridColumns;

    /**
     * @return int
     */
    public function getGridColumns()
    {
        return (int) $this->gridColumns;
    }

    /**
     * @param int $gridColumns
     */
    public function setGridColumns($gridColumns)
    {
        $this->gridColumns = (int) $gridColumns;
    }



    public function getName()
    {
        return self::NAME;
    }

    /**
     * <columns small="{small}" large="{large}">{inner}</columns>
     * --------------------------------------------------------
     * <th class="small-{small} large-{large} columns">
     *      <table>
     *          <tr>
     *              <th>
     *                  {inner}
     *              </th>
     *              <th class="expander"></th>
     *          </tr>
     *      </table>
     * </th>
     *
     *
     * @param HtmlNode $element
     * @param Inky $inkyInstance
     *
     * @return HtmlNode
     */
    public function parse(HtmlNode $element, Inky $inkyInstance)
    {
        // This is a hack for no-expander because PhpDomParser doesn't seem to support value-less attributes.
        $outerHtml = $element->outerHtml();
        if(!$element->getAttribute('no-expander') && stristr($outerHtml, 'no-expander'))
        {
            $element->setAttribute('no-expander', 'true');
        }

        $this->setGridColumns($inkyInstance->getGridColumns());
        $th = $this->th($this->getUsableAttributes($element));
        $th->setAttribute('class', $this->prepareCssClass($element));
        $table = $this->table();
        $tr = $this->tr();
        $childTh = $this->th();
        $isExpanding = (bool) (is_null($element->getAttribute('no-expander')) || $element->getAttribute('no-expander') == 'false');
        $hasRowChildren = $this->hasRowChild($element, $inkyInstance); // must be called before children are moved
        $this->copyChildren($element, $childTh);
        $tr->addChild($childTh);
        //if element contains as <row />
        if($hasRowChildren && $isExpanding) {
            $expander = $this->th(array('class' => 'expander'));
            $tr->addChild($expander);
        }
        $table->addChild($tr);
        $th->addChild($table);

        return $th;
    }

    protected function getColumnCount(HtmlNode $element)
    {
        $count = 0;
        foreach($element->getParent()->getChildren() as $sibling) {
            /**  */
            if($sibling instanceof HtmlNode) {
                $count++;
            }
        }
        return $count;
    }

    protected function isLastColumn(HtmlNode $element)
    {
        $isLast = false;
        foreach($element->getParent()->getChildren() as $column) {
            if($column instanceof HtmlNode) {
                $isLast = $element->id() === $column->id();
            }
        }
        return $isLast;
    }

    protected function isFirstColumn(HtmlNode $element)
    {
        $isFirst = false;
        foreach(array_reverse($element->getParent()->getChildren()) as $column) {
            if($column instanceof HtmlNode) {
                $isFirst = $element->id() === $column->id();
            }
        }
        return $isFirst;
    }

    /**
     * @param HtmlNode $element
     * @return string
     */
    protected function prepareCssClass(HtmlNode $element)
    {
        // Prepare CSS Class
        $cssClasses = array($this->getName());
        if($element->getAttribute('class')) {
            $cssClasses[] = $element->getAttribute('class');
        }
        // small
        $smallCount = (int) $element->getAttribute('small');
        if($smallCount === 0) {
            $smallCount = $this->getGridColumns();
        }
        $cssClasses[] = sprintf('small-%s', $smallCount);
        // large
        $largeCount = (int) $element->getAttribute('large');
        if($largeCount === 0) {
            $columnCount = $this->getColumnCount($element);
            $largeCount = floor($this->getGridColumns()/$columnCount);
        }
        $cssClasses[] =  sprintf('large-%s', $largeCount);
        //last
        if($this->isLastColumn($element)) {
            $cssClasses[] = 'last';
        }
        //first
        if($this->isFirstColumn($element)) {
            $cssClasses[] = 'first';
        }

        return implode(' ', $cssClasses);
    }

    protected function copyChildren(HtmlNode $fromElement, HtmlNode $toElement)
    {

        $newNodeChildren = $fromElement->getChildren();
        foreach($newNodeChildren as $child) {
            $toElement->addChild($child);
        }
        return $newNodeChildren;

    }

    protected function hasRowChild(HtmlNode $element, Inky $inkyInstance)
    {
        $rowTags = $inkyInstance->getAllAliasForTagName('row');
        //check if element is a row
        if($this->elementHasCssClass($element, 'row') || in_array($element->getTag()->name(), $rowTags)) {
            return true;
        }
        foreach($element->getChildren() as $child) {
            if($child instanceof HtmlNode && $this->hasRowChild($child, $inkyInstance)) {
                return true;
            }
        }
        return false;
    }

}
