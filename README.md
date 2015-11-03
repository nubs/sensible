# sensible
A PHP library for finding sensible user programs, like editor, pager, and browser.

[![Build Status](http://img.shields.io/travis/nubs/sensible.svg?style=flat)](https://travis-ci.org/nubs/sensible)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/nubs/sensible.svg?style=flat)](https://scrutinizer-ci.com/g/nubs/sensible/)
[![Code Coverage](http://img.shields.io/coveralls/nubs/sensible.svg?style=flat)](https://coveralls.io/r/nubs/sensible)

[![Latest Stable Version](http://img.shields.io/packagist/v/nubs/sensible.svg?style=flat)](https://packagist.org/packages/nubs/sensible)
[![Total Downloads](http://img.shields.io/packagist/dt/nubs/sensible.svg?style=flat)](https://packagist.org/packages/nubs/sensible)
[![License](http://img.shields.io/packagist/l/nubs/sensible.svg?style=flat)](https://packagist.org/packages/nubs/sensible)

[![Dependency Status](https://www.versioneye.com/user/projects/53866d7014c15895cb000053/badge.svg?style=flat)](https://www.versioneye.com/user/projects/53866d7014c15895cb000053)

## Requirements
This library requires PHP 5.5, or newer.

## Installation
This package uses [composer](https://getcomposer.org) so you can just add
`nubs/sensible` as a dependency to your `composer.json` file or execute the
following command:

```bash
composer require nubs/sensible
```

## Supported Program Types
This library supports opening a text editor (like vim), a pager (like more),
and a browser (like firefox).

## Program Factories
The recommended way to initialize one of the supported program loaders is to
use the included factories.

### Browser Factory
The browser factory uses a command locator (via [which]) to determine which
browsers are available.  The default list of browsers is
* sensible-browser
* firefox
* chromium-browser
* chrome
* elinks

A simple example for creating a browser object:
```php
$commandLocatorFactory = new Nubs\Which\LocatorFactory\PlatformLocatorFactory();
$browserFactory = new Nubs\Sensible\CommandFactory\BrowserFactory(
    $commandLocatorFactory->create()
);
$browser = $browserFactory->create();
```

If you want to override the default list of browsers:
```php
$commandLocatorFactory = new Nubs\Which\LocatorFactory\PlatformLocatorFactory();
$browserFactory = new Nubs\Sensible\CommandFactory\BrowserFactory(
    $commandLocatorFactory->create(),
    ['my-favorite-browser', 'some-fallback-browser']
);
$browser = $browserFactory->create();
```

### Editor Factory
The editor factory uses your `EDITOR` environment variable if set, otherwise it
uses a command locator (via [which]) to determine which editors are available.
The default list of editors is
* sensible-editor
* nano
* vim
* ed

A simple example for creating a editor object:
```php
$commandLocatorFactory = new Nubs\Which\LocatorFactory\PlatformLocatorFactory();
$editorFactory = new Nubs\Sensible\CommandFactory\EditorFactory(
    $commandLocatorFactory->create()
);
$editor = $editorFactory->create();
```

If you want to override the default list of editors:
```php
$commandLocatorFactory = new Nubs\Which\LocatorFactory\PlatformLocatorFactory();
$editorFactory = new Nubs\Sensible\CommandFactory\EditorFactory(
    $commandLocatorFactory->create(),
    ['my-favorite-editor', 'some-fallback-editor']
);
$editor = $editorFactory->create();
```

### Pager Factory
The pager factory uses your `PAGER` environment variable if set, otherwise it
uses a command locator (via [which]) to determine which pagers are available.
The default list of pagers is
* sensible-pager
* less
* more

A simple example for creating a pager object:
```php
$commandLocatorFactory = new Nubs\Which\LocatorFactory\PlatformLocatorFactory();
$pagerFactory = new Nubs\Sensible\CommandFactory\PagerFactory(
    $commandLocatorFactory->create()
);
$pager = $pagerFactory->create();
```

If you want to override the default list of pagers:
```php
$commandLocatorFactory = new Nubs\Which\LocatorFactory\PlatformLocatorFactory();
$pagerFactory = new Nubs\Sensible\CommandFactory\PagerFactory(
    $commandLocatorFactory->create(),
    ['my-favorite-pager', 'some-fallback-pager']
);
$pager = $pagerFactory->create();
```

## Using the programs
Once you've created the program type with its strategy for locating the
sensible command for the user, you can use it to work with files/data/etc.

### Browser
A browser can be executed to load a supported URI.  For example:
```php
$browser->viewURI(
    new Symfony\Component\Process\ProcessBuilder(), 
    'http://www.google.com'
);
```

### Editor
The editor can be used to edit files.  For example:
```php
$process = $editor->editFile(
    new Symfony\Component\Process\ProcessBuilder(), 
    '/path/to/a/file'
);
if ($process->isSuccessful()) {
    // continue
}
```

There is also a convenient shorthand for editing a string in an editor by means
of a temporary file.  For example:
```php
$updatedMessage = $editor->editData(
    new Symfony\Component\Process\ProcessBuilder(), 
    'a message'
);
```

This will return the input unaltered if the process does not exit successfully.

### Pager
The pager passes the file or string to the configured pager for convenient
viewing.  For example, for a file source:
```php
$process = $pager->viewFile(
    new Symfony\Component\Process\ProcessBuilder(), 
    '/path/to/a/file'
);
```

Or for a string source:
```php
$process = $pager->viewData(
    new Symfony\Component\Process\ProcessBuilder(), 
    'a message'
);
```

## CLI Interface
There is also a CLI interface for Linux systems that imitates Ubuntu's
`sensible-*` commands. It is available as [`nubs/sensible-cli`][sensible-cli].

## License
sensible is licensed under the MIT license.  See [LICENSE](LICENSE) for the
full license text.

[which]: https://github.com/nubs/which
[sensible-cli]: https://github.com/nubs/sensible-cli
