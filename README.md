# Kabar framework

Framework speeding up WordPress development

Framework goals:
* Provide object oriented, modular, SOLID, DI based framework for WordPress development
* Allowing for easy testing of buisness logic
* Speed up development by providing ready to use solutions for common problems
    - creating forms
    - common controls not implemented in WordPress (color picker, slider, multi-select etc.)
    - extending UI of posts, taxonomies, user profiles
    - global configuration object allowing for hassle free usage of customizer
    - cache & partial cache invalidation
    - collecting and outputing - otherwise - inline styles
    - creating reusable UI components, allowing for easy composing pages from widgets
    - shitload of other stuff that I am implementing right now

This project uses semantic versioning (http://semver.org/spec/v2.0.0.html).

Until version 1.0 is out breaking changes will be introduced and API will change a lot. You have been warned. Also proper documentation and examples are work in progress, so I advice against trying to use it right now. Spare youtself frustration and wait for proper guide explaining how and why things are done in that particular way.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gniewomir/kabar/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gniewomir/kabar/?branch=master)

[![Build Status](https://scrutinizer-ci.com/g/gniewomir/kabar/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gniewomir/kabar/build-status/master)

# Todo

### Testing
* Integration tests
* Performance benchmarks
* Greater unit tests coverage

### Code quality
* Clear Scrutinizer backlog
* Rely more on abstractions instead specific classes
* Break interfaces into a smaller ones

### Deployment & documentation
* Publish as Composer package
* Proper examples and tutorials
* Proper build process for JS and CSS contained in library

# Changelog

### Current iteration

#### Done
* Change versioning form 2.xx to 0.xx, to allow sticking to semantic versioning spec
* Form, Fields and Storage classes moved to utility namespace
* UI components (Taxonomy, User, Metabox etc.)
* getForId(), saveForId(), searchStorage() methods added to form fields classes
* Storage objects updated() method now allows setting updated value
* Introduce Dice Dependancy Injection container in place of service locator
* Extend Dice to automaticaly share objects in selected namespaces
* Rewrite ServiceLocator to act as facade for Dice to keep library mostly backward compatibile
* Replaced 'module callbacks' with callbacks
* Configured Unit Testing for Scrutinizer.ci
* Found a way around eval in WP_Widget decorator
* Widgets factory, taking care of setting up widgets

#### In progress
* Add tests for basic components (fields, storage, form?)
* Finish kabar widgets feature complete refactoring
* Exceptions throwing/handling instead trigger error

#### Backlog
* Widget forms as Form component, widget fields based on \kabar\Utitlity\Fields classes
* Introduce switchable cache strategies for cache module
* Refactor cache class to reduce cyclomatic complexity
* Refactor config class to reduce cyclomatic complexity and make it more efficient
* Widget cache class
* Form factory
* Fields factory
* Customize control decorator, allowing use of our field objects
* Post type object, taking taxonomy and UI objects as optional dependancies
* Loop module, returning populated templates for query
* CreateOn/CreateIf methods for container, to allow donditional module creation?
* Router module (https://carlalexander.ca/designing-system-wordpress-routing/#more-1209)
* Use Storage object for Config module
* Rewrite fieldset field/add fieldset handling to form component
* Add "Table" templates for all fields
* Package definition for composer
* Form fields validator objects
* AJAX forms
* Select for page template for widgetized pages
* Panel support for Config/Customization module
* Inline styles served as file
* Events service
* Image sizes handling, srcset handling
* Full Multisite support
* Full translation support
* Full WPML support
* Twig in place of bare-bones templating?
* Allow easier overriding library objects from application root

### 0.38.0

#### Features
* Template factory

### 0.37.3

#### Features
* getField method for form component
* Svg module, allow sanitized Svg uploads
* Taxonomy Term module for extending term add/edit screen and handling term meta
* Term meta storage object
* Added update callback for forms to allow cache clearing

#### Bugfix
* (breaking) Cache module injected to Styles module
* TaxTerm component and UserProfile module no longer retreive settings straight from storage object bypasing field object
* Form field when cloned makes copy of storage object

### 0.32.2

#### Features
* Site options storage object
* Checking if field has storage object assigned
* Form component now can return populated template

#### Bugfix
* Don't try to cache empty global configuration
* Fixed bug preventing select control from displaying in customizer

### 0.29.1

#### Features
* Internal 'form' field. It't won't be rendered when displaying or saved when submiting form, but allows to keep data in the same context

#### Bugfix
* Removed bug preventing diplaying default image in image/library image form fields image preview

### 0.28.8

#### Features
* HTML form field with WYSIWYG editor
* Storge objects now allow for searching id by key/value pair
* Library image form field - retricting selected images to uploaded ones
* Image form/Library image field clearing

#### Bugfix
* Removed bug, that was causing UserProfile module to save user meta values to current user
* Removed bug, that was causing that checkboxes wasn't showing as checked, even when they was
* Removed bug, that was causing that checkboxes to not returning correct default value
* Refactored checkbox class critical methods
* Fixed textarea width on WYSIWYG form field in profile pages
* Simplified storage objects interface

### 0.25.0
* Small refactorings, doc blocks updates resulting from Scrutinizer.ci reports

### 0.24.4

#### Features
* Added 'UserProfile' module for extending user profile page
* Added 'UserMeta' storage strategy, other strategies code cleanup
* New form field 'Show' which hides/shows all other fields in parent container
* Refactored Metabox component to use Form component instead duplicating code
* Removed Google Map and Mail components

#### Other
* Included fields css as part of 'Form' component
* All components was made final
* Refactored form fields, to allow changing field template
* Refactoring form fields java script, to make code more consistent

### 0.19.0
* Added 'AdminNotices' module
* Added new functions to base module class, to check if required actions was or wasn't executed already
* Updated plugin modules to use new functions

### 0.18.0
* Added 'Scripts' module for passing data from php to java script

### 0.17.3
* Allow modules to register their own customization/config sections

### 0.17.2
* Fixed bug causing detecting widgetized page, when category slug matches page slug

### 0.17.1
* Added dedicated module for widgetized pages sidebars to simplify pages module
* Moved cache purging for sidebars to separate class

### 0.16.1
* Change utility classes naming scheme
* Updated documentation (work in progress)

### 0.16.0
* Moved project to GitHub and wrapped in plugin