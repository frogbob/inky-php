# InkyPHP

![InkyPHP](http://api.devbar.ultrabold.de/github?text=InkyPHP)

**PHP Implementation of ZURB's Foundation for Email parser - Based on thampe/inky**

This package compiles ZURB's template-language 'Inky' to it's html version 
to build responsive email-templates.

It's based on [thampe/inky](https://github.com/thampe/inky/blob/master/README.md) which unfortunately is abandoned.

### Docs

* [Installation](#installation)
* [Usage and Examples](#usage-and-examples)
* [License](#license)

## Installation 

### Install package

Add the package in your composer.json by executing the command.

```bash
composer require frogbob/inky-php
```

## Usage and Examples

### Basic Usage.

```php
<?php
use Frogbob\InkyPHP\InkyPHP;

$gridColumns = 12; //optional, default is 12
$additionalComponentFactories = []; //optional
$inky = new InkyPHP($gridColumns, $additionalComponentFactories);

$inky->releaseTheKraken('html...');
```

### Add Tag-Alias

```php
<?php
use Frogbob\InkyPHP\InkyPHP;

$inky = new InkyPHP();
$inky->addAlias('test', 'callout')

$inky->releaseTheKraken('<test>123</test>'); //equal to "<callout>123</callout>"
```

### Add your own component factory

Add your own component factory, to convert custom HTML-Tags.

```php
<?php

use Frogbob\InkyPHP\Component\ComponentFactoryInterface;
use Frogbob\InkyPHP\InkyPHP;
use PHPHtmlParser\Dom\HtmlNode;

class TestComponentFactory implements ComponentFactoryInterface
{
    public function getName()
    {
        return 'test' // name of the html tag.
    }

    public function parse(HtmlNode $element, InkyPHP $inkyInstance)
    {
        // ...
    }
}

$inky = new InkyPHP();
$inky->addComponentFactory(new TestComponentFactory());
$inky->releaseTheKraken('<test></test>');
```

## License
See the [LICENSE](LICENSE) file for license info (it's the MIT license).
