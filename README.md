CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Twig


INTRODUCTION
------------

Provides modal API for elements and fields.


REQUIREMENTS
------------

This module requires no modules outside of Drupal core.


INSTALLATION
------------

Install as you would normally install a contributed Drupal module. Visit
https://www.drupal.org/node/1897420 for further information.


TWIG
----

A twig helper has been provided that will convert a render element into a modal.
The "breakpoint" setting will automatically set the conversion point of the
modal.

In this example, the first param is the trigger title. The second is the modal
options (because we set the breakpoint to 'md', the modal will only be used
when the screen size is below the medium breakpoint). The third is the preset.
The fourth is the attributes that will be applied to the trigger.

```twig
{{ form|neo_modal('Filter By'|t, {title: 'Filter Products', breakpoint: 'md'}, 'neo_modal_shelf_left', {class: ['btn btn-outline bg-base-0 w-full text-center']}) }}
```
