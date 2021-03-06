# kabar library

Set of modules and components for speeding up WordPress development contained in a plugin.

Documentation and examples are work in progress, so I advice against trying to use it right now. If you are more experienced than me (very probable), you probably have your own solution, and if not, spare youtself frustration and wait for proper guide explaining how and why things are done in that particular way.

There will be changes breaking compatibility without major version change until version 3.0 is out.

I will be happy seeing pull requests to development branch anyway. Coding standard Symfony2.

Development branch:

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gniewomir/kabar/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/gniewomir/kabar/?branch=develop)

[![Build Status](https://scrutinizer-ci.com/g/gniewomir/kabar/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/gniewomir/kabar/build-status/develop)

# Roadmap

### Features
* Form vields validator objects
* Image sizes handling, srcset handling
* Full translation support
* Panel support for Config/Customization module
* Multisite support

### Code quality
* Clear Scrutinizer backlog
* Replace 'module callbacks' with callbacks, force registering settings sections instead extending 'Config' module
* Find a way around eval in widgets module, to allow using library if it is disabled for security reasons
* Dependancy Injection Container (Dice?) in place of Service Locator
* Unit tests
* Widget fields based on \kabar\Utitlity\Fields classes

### Deployment & documentation
* Publish as Composer package
* Proper automatic documentation
* Proper examples and tutorials
* Proper build process for JS and CSS contained in library

# Changelog

### 2.32.1

#### Features
* Site options storage object
* Checking if field has storage object assigned
* Form component now can return populated template

#### Bugfix
* Fixed bug preventing select control from displaying in customizer

### 2.29.1

#### Features
* Internal 'form' field. It't won't be rendered when displaying or saved when submiting form, but allows to keep data in the same context

#### Bugfix
* Removed bug preventing diplaying default image in image/library image form fields image preview

### 2.28.8

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

### 2.25.0
* Small refactorings, doc blocks updates resulting from Scrutinizer.ci reports

### 2.24.4

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

### 2.19.0
* Added 'AdminNotices' module
* Added new functions to base module class, to check if required actions was or wasn't executed already
* Updated plugin modules to use new functions

### 2.18.0
* Added 'Scripts' module for passing data from php to java script

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