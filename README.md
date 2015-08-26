# kabar library

Basic set of libraries I use to speed up WordPress development contained in a plugin.

Documentation and examples are work in progress, so I advice against trying to use it right now. If you are more experienced than me (very probable), you probably have your own solution, and if not, spare youtself frustration and wait for proper guide explaining how and why things are done in that particular way.

I will be happy seeing pull requests to development branch anyway. Coding standard Symfony2.

Development branch:

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gniewomir/kabar/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/gniewomir/kabar/?branch=develop)

[![Build Status](https://scrutinizer-ci.com/g/gniewomir/kabar/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/gniewomir/kabar/build-status/develop)

## What I wanted to accomplish

1. Easy way to create reusable modules that I can use across multiple projects.
2. Easy way of extending those modules or replacing them.
3. Easy way of creating extendable widgets - in Object Oriented way.
4. Easy way of creating pages built from this widgets, and configurable trough WP customization screen.
5. Depending on convention, not enforcement of library core concepts.

## Terminology

* ServiceLocator - handles creating, retrieving and storing library parts
* Modules        - provide single feature for your project (one instance per request, should - but don't have to - be descendants of \kabar\Module\Module\Module)
* Widgets        - modules providing alternative way of creating WordPress widgets in Object Oriented way. (one instance per request - have to be descendants of \kabar\Widget\Widget\AbstractWidget)
* Components     - provide utility object like template, form or metabox (multiple instances per request - used by modules)
* Utility        - at the moment form fields and form storage classes.

One instance per request means that you probably should use ServiceLocator::get($moduleType, $moduleName) method to access is, as it will create instance and return it in all subsequent calls. You probably dont wan't 'Fancybox' module loaded twice. But library doesn't enforce it in any way.

```php
<?php
\kabar\ServiceLocator::get('ModuleType', 'ModuleName');
?>
```

Multiple instances per request means that you probably should use ServiceLocator::getNew($moduleType, $componentName) method to get this library part, as there is no point of storing diffrent instances of component - for example - 'Template'. They live and die for their respectable modules. getNew will component, create and return instance of it, but not store it lige get method does.

## Conventions

### Namespaces, file names, and paths

Library provides basic set of modules, components and utility classes that you can replace or extend. To do this you need register namespace and path for them. 'kabar' namespace and it's path is registered by default.

```php
<?php
\kabar\ServiceLocator::register(
    'VendorNamespace',
    $extensionsDirectory // with trailing slash
);
?>
```

When you are trying to get instance of module:

```php
<?php
\kabar\ServiceLocator::get('ModuleType', 'ModuleName');
?>
```

Library assumes that path to module - may - be:

```php
$path = $extensionsDirectory.'/ModuleType/ModuleName/ModuleName.php';
```

And its class name and namespace will be defined as below:

```php
<?php
namespace VendorNamespace\ModuleType\ModuleName\

class ModuleName {
    // logic
}
?>
```

Then it checks all registered pairs VendorNamespace/path in reverse order (registered last will be tested first). If in path for tested namespace exists a directory matching scheme explained above ( $extensionsDirectory.'/ModuleType/ModuleName/ ) module placed there will be loaded, and used from now on.

For example, if you want to replace or extend core 'Config' module, you just register your namespace and path, and then, when you will try to get 'Config' module instance, it will retrieve your version instead default one.

For example you may extend default 'Config' module like this:

```php
<?php
namespace VendorNamespace\Module\Config;

use \kabar\ServiceLocator as ServiceLocator;

class Config extends \kabar\Module\Config\Config
{
    /**
     * Returns config array
     * @return array
     */
    protected function getConfig()
    {
        return array(
            'featured' => array(
                'sectionTitle'         => 'Promowana treść',
                'sectionCapability'    => 'edit_theme_options',
                'featuredCategorySlug' => array(
                    'type'    => 'select',
                    'default' => 'featured',
                    'choices' => array('FeaturedPosts', 'getSelectChoices'),
                    'label'   => __('Featured posts category', 'textdomain');
                )
            )
        );
    }
}
?>
```

And then you can get it in exactly same way, as you would do with default one:

```php
<?php
$config = \kabar\ServiceLocator::get('Module', 'Config');
?>
```

If you haven't created your own version of the config module - example above will return default one.

Module instance is created at first request for this module or when other, already created module requests it, to ensure that unsused parts of library don't eat up resources.

## Core modules

#### Cache

Documentation coming soon.

#### Styles

Documentation coming soon.

#### Config

Documentation coming soon.

#### Sidebars

Documentation coming soon.

#### Pages

Documentation coming soon.

# Changelog

### 2.17.3
* Allow modules to register their own customization/config sections

### 2.17.2
* Fixed bug causing detecting widgetized page, when category slug matches page slug

### 2.17.1
* Added dedicated module for widgetized pages sidebars to simplify pages module
* Moved cache purging for sidebars to separate class

### 2.16.1
* Change utility classes naming scheme
* Updated documentation (work in progress)

### 2.16.0
* Moved project to GitHub and wrapped in plugin