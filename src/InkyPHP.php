<?php

namespace Frogbob\InkyPHP;


use Frogbob\InkyPHP\Component\CalloutFactory;
use Frogbob\InkyPHP\Component\BlockGridFactory;
use Frogbob\InkyPHP\Component\ButtonFactory;
use Frogbob\InkyPHP\Component\CenterFactory;
use Frogbob\InkyPHP\Component\ColumnsFactory;
use Frogbob\InkyPHP\Component\ComponentFactoryInterface;
use Frogbob\InkyPHP\Component\ContainerFactory;
use Frogbob\InkyPHP\Component\InkyFactory;
use Frogbob\InkyPHP\Component\MenuFactory;
use Frogbob\InkyPHP\Component\MenuItemFactory;
use Frogbob\InkyPHP\Component\RawFactory;
use Frogbob\InkyPHP\Component\RowFactory;
use Frogbob\InkyPHP\Component\SpacerFactory;
use Frogbob\InkyPHP\Component\WrapperFactory;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\AbstractNode;
use PHPHtmlParser\Dom\Collection;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\InnerNode;
use PHPHtmlParser\Exceptions\CircularException;

class InkyPHP
{

    protected $alias = array();

    protected $componentFactory = array();

    /**
     * @var int
     */
    protected $gridColumns;

    protected $options = [
        'whitespaceTextNode' => true,
        'strict'             => false,
        'enforceEncoding'    => null,
        'cleanupInput'       => true,
        'removeScripts'      => false,
        'removeStyles'       => false,
        'preserveLineBreaks' => false,
        'removeDoubleSpace'  => true,
    ];

    public function __construct($gridColumns = 12, $componentFactories = array(), $options = array())
    {
        $this->setGridColumns($gridColumns);
        $this->addComponentFactory(new RowFactory());
        $this->addComponentFactory(new ContainerFactory());
        $this->addComponentFactory(new ButtonFactory());
        $this->addComponentFactory(new InkyFactory());
        $this->addComponentFactory(new BlockGridFactory());
        $this->addComponentFactory(new MenuFactory());
        $this->addComponentFactory(new MenuItemFactory());
        $this->addComponentFactory(new ColumnsFactory());
        $this->addComponentFactory(new CalloutFactory());
        $this->addComponentFactory(new SpacerFactory());
        $this->addComponentFactory(new WrapperFactory());
        $this->addComponentFactory(new CenterFactory());
        $this->addComponentFactory(new RawFactory());

        $this->config = array_merge($this->config, $config);

        foreach($componentFactories as $componentFactory) {
            if($componentFactory instanceof ComponentFactoryInterface) {
                $this->addComponentFactory($componentFactory);
            }
        }
    }

    /**
     * @return int
     */
    public function getGridColumns()
    {
        return $this->gridColumns;
    }

    /**
     * @param int $gridColumns
     */
    public function setGridColumns($gridColumns)
    {
        $this->gridColumns = (int) $gridColumns;
    }

    /**
     * Adds an alisa for a component
     *
     * @param $alias
     * @param $tagName
     * @return $this
     */
    public function addAlias($alias, $tagName)
    {
        if($this->getComponentFactory($tagName)) {
            $this->alias[(string) $alias] = (string) $tagName;
        }
        return $this;
    }

    /**
     * removes an alias
     *
     * @param $alias string
     * @return $this
     */
    public function removeAlias($alias)
    {
        unset($this->alias[$alias]);
        return $this;
    }

    public function getAllAliasForTagName($tagName)
    {
        $allAlisa = array($tagName);
        foreach($this->alias as $alias => $tag) {
            if($tag == $tagName) {
                $allAlisa[] = $alias;
            }
        }
        return $allAlisa;
    }

    /**
     * returns a Component Factory for a given tag or alias
     *
     * @param $tagName
     * @return null|ComponentFactoryInterface
     */
    public function getComponentFactory($tagName)
    {
        //check for alias first
        if(isset($this->alias[$tagName])) {
            $tagName = $this->alias[$tagName];
        }

        if(
            isset($this->componentFactory[$tagName])
            && $this->componentFactory[$tagName] instanceof ComponentFactoryInterface
        ) {
            return $this->componentFactory[$tagName];
        }

        return null;
    }

    /**
     * add a Component Factory
     *
     * @param ComponentFactoryInterface $componentFactory
     * @return $this
     */
    public function addComponentFactory(ComponentFactoryInterface $componentFactory)
    {
        $this->componentFactory[$componentFactory->getName()] = $componentFactory;
        return $this;
    }

    /**
     * @return ComponentFactoryInterface[]
     */
    protected function getAllComponentFactories()
    {
        $factories = $this->componentFactory;
        foreach($this->alias as $alias => $name) {
            if($factory = $this->getComponentFactory($name)) {
                $factories[$alias] = $factory;
            }
        }
        return $factories;
    }

    /**
     * @param $html
     * @return string
     * @throws CircularException
     */
    public function releaseTheKraken($html)
    {
        $dom = new Dom();
        $dom->setOptions($this->options);
        $dom->load((string) $html);

        $parseCounter = 0;
        while($this->parse($dom)) {
            $parseCounter++;
            if($parseCounter >= 100) {
                throw new CircularException('Inky reached max parsing runs of '.$parseCounter);
            }
        };
        $this->clearCache($dom->root);
        return $dom->root->outerhtml;
    }

    protected function clearCache(InnerNode $node)
    {
        foreach($node->getChildren() as $child) {
            if($child instanceof AbstractNode) {
                if($child instanceof InnerNode) {
                    $this->clearCache($child);
                }
                $node->removeChild($child->id());
                $node->addChild($child);
            }
        }
    }

    protected function parse(Dom $dom)
    {
        $parseInComplete = false;
        foreach($this->getAllComponentFactories() as $tag => $factory) {
            $elements = $dom->getElementsByTag($tag);
            foreach($elements as $element) {
                /** @var AbstractNode|Collection $element */
                $newElement = $factory->parse($element, $this);

                $collection = new Collection();
                if($newElement instanceof Collection)
                {
                    $collection = $newElement;
                }
                elseif($newElement instanceof AbstractNode)
                {
                    $collection[] = $newElement;
                }

                if(!$collection->count())
                {
                    continue;
                }

                // replace element
                if($collection[0]->id() !== $element->id()) {
                    $parseInComplete = true;
                    //replace element with new element
                    $parent = $element->getParent();
                    if(!$parent instanceof InnerNode) {
                        continue;
                    }

                    $siblings = $parent->getChildren();
                    foreach($siblings as $sibling) {
                        /** @var $sibling HtmlNode*/
                        $parent->removeChild($sibling->id());
                        if($sibling->id() === $element->id()) {
                            foreach($collection->toArray() as $elementToAdd) {
                                $parent->addChild($elementToAdd);
                            }
                        }else {
                            $parent->addChild($sibling);
                        }
                    }
                }
            }
        }
        return $parseInComplete;
    }

}
