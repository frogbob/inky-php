<?php

namespace Frogbob\InkyPHP\Component;

use PHPHtmlParser\Dom\HtmlNode;

abstract class AbstractComponentFactory implements ComponentFactoryInterface
{

    protected function copyChildren(HtmlNode $fromElement, HtmlNode $toElement)
    {
        $newNodeChildren = $fromElement->getChildren();
        foreach($newNodeChildren as $child) {
            $toElement->addChild($child);
        }
        return $newNodeChildren;
    }

    protected function node($tag, $attributes = array())
    {
        $node = new HtmlNode($tag);
        foreach($attributes as $key => $attribute) {
            $node->setAttribute($key, $attribute);
        }
        return $node;
    }

    protected function table($attributes = array())
    {
        return $this->node('table', $attributes);
    }

    protected function tbody($attributes = array())
    {
        return $this->node('tbody', $attributes);
    }

    protected function tr($attributes = array())
    {
        return $this->node('tr', $attributes);
    }

    protected function td($attributes = array())
    {
        return $this->node('td', $attributes);
    }

    protected function th($attributes = array())
    {
        return $this->node('th', $attributes);
    }

    protected function img($attributes = array())
    {
        $node = $this->node('img', $attributes);
        $node->getTag()->selfClosing();
        return $node;
    }

    protected function elementHasCssClass(HtmlNode $element, $cssClass)
    {
        $class = $element->getAttribute('class');
        return is_string($class) && strpos($class, $cssClass) !== false;
    }

    protected function addCssClass($cssClass, HtmlNode $element)
    {
        $element->setAttribute('class', trim($cssClass . ' ' . $element->getAttribute('class')));
    }

    protected function getUsableAttributes(HtmlNode $element, $exclude = [])
    {
        $exclude = (array) $exclude;
        $ignoredAttributes = ['href', 'size', 'size-sm', 'size-lg', 'large', 'no-expander', 'small', 'target'];

        $newAttributes = [];
        foreach($element->getAttributes() as $attribute => $value)
        {
            if(!in_array($attribute, $ignoredAttributes) && !in_array($attribute, $exclude))
            {
                $newAttributes[$attribute] = $value;
            }
        }

        return $newAttributes;
    }
}
