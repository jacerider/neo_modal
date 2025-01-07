# Neo Modal

## INTRODUCTION

The Neo Modal module is a modern, flexible modal dialog system for Drupal that provides enhanced user experience through customizable modals, panels, and dialogs. It extends Drupal's core dialog functionality with additional features and a more modern interface.

The primary use cases for this module are:

- Creating slide-in panels from any edge of the screen (right, left, top, bottom)
- Building modern, animated modal dialogs with customizable transitions
- Implementing stacked modal interfaces for complex user interactions
- Enhancing form interactions with modal/panel-based workflows
- Creating responsive and accessible modal experiences

## REQUIREMENTS

This module requires the following:

- Drupal 9.x or 10.x
- Neo Build module (for styling and animations)
- jQuery (included in Drupal core)

## INSTALLATION

1. Install as you would normally install a contributed Drupal module.
   See: https://www.drupal.org/node/895232 for further information.

2. Enable the module:
   ```bash
   drush en neo_modal
   ```

## CONFIGURATION

### Basic Setup
1. Navigate to Administration → Configuration → User Interface → Neo Modal Settings
2. Configure default settings for modals and panels:
   - Default animation speed
   - Default placement
   - Default width/height
   - Backdrop settings

### Usage Examples

#### 1. Basic Modal Dialog
```php
$modal = new Neo\Modal([
  'trigger' => $element,
  'width' => '600px',
  'backdrop' => true,
  'bodyLock' => true
]);
```

#### 2. Side Panel
```php
$modal = new Neo\Modal([
  'trigger' => $element,
  'placement' => 'right',
  'width' => '400px',
  'contentAnimateIn' => 'slideInRight',
  'contentAnimateOut' => 'slideOutRight'
]);
```

#### 3. AJAX Integration
```php
$response = new AjaxResponse();
$response->addCommand(new OpenNeoModalCommand(
  'Modal Title',
  $content,
  [
    'placement' => 'right',
    'width' => '400px'
  ]
));
```

### Available Options

#### Modal Properties
- `placement`: Position of the modal ('center', 'right', 'left', 'top', 'bottom')
- `width`: Width of the modal (px, %, vh)
- `height`: Height of the modal (px, %, vh)
- `backdrop`: Enable/disable backdrop
- `bodyLock`: Lock body scrolling when modal is open
- `closeButton`: Position of close button ('start', 'end', false)

#### Animation Properties
- `contentAnimateIn`: Animation for opening ('fadeIn', 'slideInRight', etc.)
- `contentAnimateOut`: Animation for closing ('fadeOut', 'slideOutRight', etc.)
- `contentAnimateInSpeed`: Animation speed ('fastest', 'fast', 'normal', 'slow')
- `contentAnimateOutSpeed`: Animation speed for closing
- `closeButtonAnimateIn`: Close button animation
- `closeButtonAnimateInDelay`: Delay before showing close button

## THEMING

### Template Override
Override `neo-modal.html.twig` in your theme to customize modal markup:
```twig
{% if attributes %}
<div{{ attributes }}>
  {% if close_button %}
    <button{{ close_button_attributes }}>
      <span class="neo-icon neo-icon-close"></span>
    </button>
  {% endif %}
  {{ content }}
</div>
{% endif %}
```

### CSS Customization
Target Neo Modal elements using these classes:
- `.neo-modal`: Main modal container
- `.neo-modal--right`: Right-side panel
- `.neo-modal__content`: Modal content wrapper
- `.neo-modal__close`: Close button

## TROUBLESHOOTING

Common issues and solutions:

1. **Modal not appearing**
   - Check if Neo Build module is enabled
   - Verify jQuery is loaded
   - Check browser console for errors

2. **Animations not working**
   - Ensure animation classes are correctly spelled
   - Verify Neo Build CSS is loaded
   - Check browser compatibility

3. **Multiple modals conflict**
   - Use unique identifiers for each modal
   - Manage z-index appropriately
   - Consider using modal stacking feature

## MAINTAINERS

Current maintainers for Drupal 10:
- Jace (jacerider) - https://www.drupal.org/u/jacerider

## CONTRIBUTING

1. Submit bug reports and feature requests to the [issue queue](https://www.drupal.org/project/issues/neo_modal)
2. Follow Drupal coding standards
3. Include tests with new features
4. Update documentation for significant changes

## LICENSE

This project is licensed under the GNU General Public License v2.0 or later.
