<?php

declare(strict_types = 1);

namespace Drupal\neo_modal;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Template\Attribute;
use Drupal\neo_icon\IconRepositoryTrait;
use Drupal\neo_settings\SettingsTrait;

/**
 * A modal.
 */
class Modal {

  use SettingsTrait;
  use IconRepositoryTrait;

  /**
   * The settings ID.
   *
   * @var string|null
   */
  protected string|null $settingsId = 'neo_modal.settings';

  /**
   * The libraries.
   *
   * @var array
   */
  protected array $libraries = [];

  /**
   * The trigger attributes.
   *
   * @var \Drupal\Core\Template\Attribute
   */
  protected Attribute $triggerAttributes;

  /**
   * The append to selector.
   *
   * @var string|null
   */
  protected string|null $appendTo = NULL;

  /**
   * The append to closest selector.
   *
   * @var string|null
   */
  protected string|null $appendToClosest = NULL;

  /**
   * The color scheme of the modal.
   *
   * @var string|null
   */
  protected string|null $colorScheme = NULL;

  /**
   * The width of the modal.
   *
   * @var string|null
   */
  protected string|null $width = NULL;

  /**
   * The height of the modal.
   *
   * @var string|null
   */
  protected string|null $height = NULL;

  /**
   * Whether the modal has a backdrop.
   *
   * @var bool|null
   */
  protected bool|null $backdrop = NULL;

  /**
   * Whether the modal has a header.
   *
   * @var bool|null
   */
  protected bool|null $header = NULL;

  /**
   * Whether the modal header placement is in the content.
   *
   * @var bool|null
   */
  protected bool|null $headerInContent = NULL;

  /**
   * Whether the modal has a footer.
   *
   * @var bool|null
   */
  protected bool|null $footer = NULL;

  /**
   * Whether the modal has a download link.
   *
   * @var bool|null
   */
  protected bool|null $downloadLink = NULL;

  /**
   * Whether the modal has a share link.
   *
   * @var bool|null
   */
  protected bool|null $shareLink = NULL;

  /**
   * Whether the modal has a copy link.
   *
   * @var bool|null
   */
  protected bool|null $copyLink = NULL;

  /**
   * Whether the modal content should scroll.
   *
   * @var bool|null
   */
  protected bool|null $contentScroll = NULL;

  /**
   * The title of the modal.
   *
   * @var string|null
   */
  protected string|null $title = NULL;

  /**
   * The subtitle of the modal.
   *
   * @var string|null
   */
  protected string|null $subtitle = NULL;

  /**
   * The icon of the modal.
   *
   * @var string|null
   */
  protected string|null $icon = NULL;

  /**
   * The image of the modal.
   *
   * @var string|null
   */
  protected $image = NULL;

  /**
   * The video of the modal.
   *
   * @var string|null
   */
  protected $video = NULL;

  /**
   * The iframe of the modal.
   *
   * @var string|null
   */
  protected $iframe = NULL;

  /**
   * The group of the modal.
   *
   * @var string|null
   */
  protected $group = NULL;

  /**
   * Whether to generate smart actions.
   *
   * @var bool|null
   */
  protected $smartActions = NULL;

  /**
   * The group of the modal.
   *
   * @var bool|null
   */
  protected $numeration = NULL;

  /**
   * The fit of the modal.
   *
   * @var bool|null
   */
  protected $fit = NULL;

  /**
   * The flag indicating wheter the modal is draggable.
   *
   * @var bool|null
   */
  protected $drag = NULL;

  /**
   * The flag indicating wheter the body should be locked.
   *
   * @var bool|null
   */
  protected $bodyLock = NULL;

  /**
   * The close button placement.
   *
   * @var bool
   */
  protected $closeButton = NULL;

  /**
   * The content of the modal.
   *
   * @var array|string
   */
  protected $content = NULL;

  /**
   * The content padding of the modal.
   *
   * @var string|null
   */
  protected $contentPadding = NULL;

  /**
   * The placement of the modal.
   *
   * @var string|null
   */
  protected string|null $placement = NULL;

  /**
   * The element to attach the modal to.
   *
   * @var string|null
   */
  protected string|null $attach = NULL;

  /**
   * The content color of the modal.
   *
   * @var string|null
   */
  protected string|null $contentColor = NULL;

  /**
   * The content background color of the modal.
   *
   * @var string|null
   */
  protected string|null $contentColorBg = NULL;

  /**
   * The content footer color of the modal.
   *
   * @var string|null
   */
  protected string|null $contentFooterColor = NULL;

  /**
   * The content footer background color of the modal.
   *
   * @var string|null
   */
  protected string|null $contentFooterColorBg = NULL;

  /**
   * The header color of the modal.
   *
   * @var string|null
   */
  protected string|null $headerColor = NULL;

  /**
   * The header background color of the modal.
   *
   * @var string|null
   */
  protected string|null $headerColorBg = NULL;

  /**
   * The footer color of the modal.
   *
   * @var string|null
   */
  protected string|null $footerColor = NULL;

  /**
   * The footer background color of the modal.
   *
   * @var string|null
   */
  protected string|null $footerColorBg = NULL;

  /**
   * The overlay background color of the modal.
   *
   * @var string|null
   */
  protected string|null $backdropColorBg = NULL;

  /**
   * The nav color of the modal.
   *
   * @var string|null
   */
  protected string|null $navColor = NULL;

  /**
   * The nav background color of the modal.
   *
   * @var string|null
   */
  protected string|null $navColorBg = NULL;

  /**
   * The loader color of the modal.
   *
   * @var string|null
   */
  protected string|null $loaderColor = NULL;

  /**
   * The loader background color of the modal.
   *
   * @var string|null
   */
  protected string|null $loaderColorBg = NULL;

  /**
   * The placement of the attached modal.
   *
   * @var string|null
   */
  protected string|null $attachPlacement = NULL;

  /**
   * The content animation in.
   *
   * @var string|null
   */
  protected string|null $contentAnimateIn = NULL;

  /**
   * The content animation in speed.
   *
   * @var string|null
   */
  protected string|null $contentAnimateInSpeed = NULL;

  /**
   * The content animation in delay.
   *
   * @var string|null
   */
  protected string|null $contentAnimateInDelay = NULL;

  /**
   * The content animation out.
   *
   * @var string|null
   */
  protected string|null $contentAnimationOut = NULL;

  /**
   * The content animation out speed.
   *
   * @var string|null
   */
  protected string|null $contentAnimateOutSpeed = NULL;

  /**
   * The content animation out delay.
   *
   * @var string|null
   */
  protected string|null $contentAnimateOutDelay = NULL;

  /**
   * Whether the body should transition scale.
   *
   * @var bool|null
   */
  protected bool|null $bodyTransitionScale = NULL;

  /**
   * Whether the body should transition blur.
   *
   * @var bool|null
   */
  protected bool|null $bodyTransitionBlur = NULL;

  /**
   * Trigger overlay content.
   *
   * @var mixed
   */
  protected $triggerOverlay = '';

  /**
   * Trigger overlay icon.
   *
   * @var string
   */
  protected $triggerOverlayIcon;

  /**
   * Trigger overlay attributes.
   *
   * @var array
   */
  protected $triggerOverlayAttributes;

  /**
   * Constructs a new Modal.
   *
   * @param string|array $content
   *   The content of the modal.
   * @param array $options
   *   The options.
   * @param string|null $preset
   *   The preset.
   */
  public function __construct($content = NULL, array $options = [], $preset = NULL) {
    $this->triggerAttributes = new Attribute([
      'class' => ['use-neo-modal'],
    ]);
    if ($content) {
      $this->setContent($content);
    }
    if ($preset) {
      $this->setSettingsVariationId($preset);
    }
    $options += $this->getSettings()->getDiffValues();
    if (!empty($options)) {
      $class = new \ReflectionClass($this);
      foreach ($options as $key => $option) {
        $method = 'set' . ucfirst($key);
        if (method_exists($this, $method)) {
          $param = $class->getMethod($method)->getParameters()[0]->getType();
          if ((string) $param === 'bool') {
            $option = (bool) $option;
          }
          $this->$method($option);
        }
      }
    }
  }

  /**
   * Returns an array of modal placements.
   *
   * @return array
   *   An array of modal placements, where the keys represent the placement
   *   values and the values represent the human-readable labels.
   */
  public static function getPlacements():array {
    return [
      'center' => 'Center',
      'top' => 'Top',
      'top-start' => 'Top Start',
      'top-end' => 'Top End',
      'bottom' => 'Bottom',
      'bottom-start' => 'Bottom Start',
      'bottom-end' => 'Bottom End',
      'right' => 'Right',
      'right-start' => 'Right Start',
      'right-end' => 'Right End',
      'left' => 'Left',
      'left-start' => 'Left Start',
      'left-end' => 'Left End',
    ];
  }

  /**
   * Returns an array of modal attach placements.
   *
   * @return array
   *   An array of modal placements, where the keys represent the placement
   *   values and the values represent the human-readable labels.
   */
  public static function getAttachPlacements():array {
    return [
      '' => 'Auto',
      'auto-start' => 'Auto Start',
      'auto-end' => 'Auto End',
      'top' => 'Top',
      'top-start' => 'Top Start',
      'top-end' => 'Top End',
      'bottom' => 'Bottom',
      'bottom-start' => 'Bottom Start',
      'bottom-end' => 'Bottom End',
      'right' => 'Right',
      'right-start' => 'Right Start',
      'right-end' => 'Right End',
      'left' => 'Left',
      'left-start' => 'Left Start',
      'left-end' => 'Left End',
    ];
  }

  /**
   * Returns an array of modal in animations.
   *
   * @return array
   *   An array of modal animations, where the keys represent the animation
   *   values and the values represent the human-readable labels.
   */
  public static function getAnimationsIn():array {
    return [
      '' => 'None',
      'comingIn' => 'Coming In',
      'backInDown' => 'Back In Down',
      'backInLeft' => 'Back In Left',
      'backInRight' => 'Back In Right',
      'backInUp' => 'Back In Up',
      'bounceIn' => 'Bounce In',
      'bounceInDown' => 'Bounce In Down',
      'bounceInLeft' => 'Bounce In Left',
      'bounceInRight' => 'Bounce In Right',
      'bounceInUp' => 'Bounce In Up',
      'fadeIn' => 'Fade In',
      'fadeInDownSmall' => 'Fade In Down Small',
      'fadeInDown' => 'Fade In Down',
      'fadeInDownBig' => 'Fade In Down Big',
      'fadeInLeftSmall' => 'Fade In Left Small',
      'fadeInLeft' => 'Fade In Left',
      'fadeInLeftBig' => 'Fade In Left Big',
      'fadeInRightSmall' => 'Fade In Right Small',
      'fadeInRight' => 'Fade In Right',
      'fadeInRightBig' => 'Fade In Right Big',
      'fadeInUpSmall' => 'Fade In Up Small',
      'fadeInUp' => 'Fade In Up',
      'fadeInUpBig' => 'Fade In Up Big',
      'fadeInTopLeft' => 'Fade In Top Left',
      'fadeInTopRight' => 'Fade In Top Right',
      'fadeInBottomLeft' => 'Fade In Bottom Left',
      'fadeInBottomRight' => 'Fade In Bottom Right',
      'flipInX' => 'Flip In X',
      'flipInY' => 'Flip In Y',
      'lightSpeedInRight' => 'Light Speed In Right',
      'lightSpeedInLeft' => 'Light Speed In Left',
      'rotateIn' => 'Rotate In',
      'rotateInDownLeft' => 'Rotate In Down Left',
      'rotateInDownRight' => 'Rotate In Down Right',
      'rotateInUpLeft' => 'Rotate In Up Left',
      'rotateInUpRight' => 'Rotate In Up Right',
      'rollIn' => 'Roll In',
      'zoomIn' => 'Zoom In',
      'zoomInDown' => 'Zoom In Down',
      'zoomInLeft' => 'Zoom In Left',
      'zoomInRight' => 'Zoom In Right',
      'zoomInUp' => 'Zoom In Up',
      'slideInDown' => 'Slide In Down',
      'slideInLeft' => 'Slide In Left',
      'slideInRight' => 'Slide In Right',
      'slideInUp' => 'Slide In Up',
    ];
  }

  /**
   * Returns an array of modal out animations.
   *
   * @return array
   *   An array of modal animations, where the keys represent the animation
   *   values and the values represent the human-readable labels.
   */
  public static function getAnimationsOut():array {
    return [
      '' => 'None',
      'comingOut' => 'Coming Out',
      'backOutDown' => 'Back Out Down',
      'backOutLeft' => 'Back Out Left',
      'backOutRight' => 'Back Out Right',
      'backOutUp' => 'Back Out Up',
      'bounceOut' => 'Bounce Out',
      'bounceOutDown' => 'Bounce Out Down',
      'bounceOutLeft' => 'Bounce Out Left',
      'bounceOutRight' => 'Bounce Out Right',
      'bounceOutUp' => 'Bounce Out Up',
      'fadeOut' => 'Fade Out',
      'fadeOutDownSmall' => 'Fade Out Down Small',
      'fadeOutDown' => 'Fade Out Down',
      'fadeOutDownBig' => 'Fade Out Down Big',
      'fadeOutLeftSmall' => 'Fade Out Left Small',
      'fadeOutLeft' => 'Fade Out Left',
      'fadeOutLeftBig' => 'Fade Out Left Big',
      'fadeOutRightSmall' => 'Fade Out Right Small',
      'fadeOutRight' => 'Fade Out Right',
      'fadeOutRightBig' => 'Fade Out Right Big',
      'fadeOutUpSmall' => 'Fade Out Up Small',
      'fadeOutUp' => 'Fade Out Up',
      'fadeOutUpBig' => 'Fade Out Up Big',
      'fadeOutTopLeft' => 'Fade Out Top Left',
      'fadeOutTopRight' => 'Fade Out Top Right',
      'fadeOutBottomLeft' => 'Fade Out Bottom Left',
      'fadeOutBottomRight' => 'Fade Out Bottom Right',
      'flipOutX' => 'Flip Out X',
      'flipOutY' => 'Flip Out Y',
      'lightSpeedOutRight' => 'Light Speed Out Right',
      'lightSpeedOutLeft' => 'Light Speed Out Left',
      'rotateOut' => 'Rotate Out',
      'rotateOutDownLeft' => 'Rotate Out Down Left',
      'rotateOutDownRight' => 'Rotate Out Down Right',
      'rotateOutUpLeft' => 'Rotate Out Up Left',
      'rotateOutUpRight' => 'Rotate Out Up Right',
      'rollOut' => 'Roll Out',
      'zoomOut' => 'Zoom Out',
      'zoomOutDown' => 'Zoom Out Down',
      'zoomOutLeft' => 'Zoom Out Left',
      'zoomOutRight' => 'Zoom Out Right',
      'zoomOutUp' => 'Zoom Out Up',
      'slideOutDown' => 'Slide Out Down',
      'slideOutLeft' => 'Slide Out Left',
      'slideOutRight' => 'Slide Out Right',
      'slideOutUp' => 'Slide Out Up',
    ];
  }

  /**
   * Returns an array of modal animations speed.
   *
   * @return array
   *   An array of modal animations, where the keys represent the animation
   *   values and the values represent the human-readable labels.
   */
  public static function getAnimationsSpeed():array {
    return [
      '' => 'Default',
      'slow' => 'Slow',
      'slower' => 'Slower',
      'slowest' => 'Slowest',
      'fast' => 'Fast',
      'faster' => 'Faster',
      'fastest' => 'Fastest',
    ];
  }

  /**
   * Returns an array of modal animations delay.
   *
   * @return array
   *   An array of modal animations, where the keys represent the animation
   *   values and the values represent the human-readable labels.
   */
  public static function getAnimationsDelay():array {
    return [
      '' => 'None',
      'default' => 'Default',
      'slow' => 'Slow',
      'slower' => 'Slower',
      'slowest' => 'Slowest',
      'fast' => 'Fast',
      'faster' => 'Faster',
      'fastest' => 'Fastest',
    ];
  }

  /**
   * Returns an array of modal header placements.
   *
   * @return array
   *   An array of modal header placements, where the keys represent the
   *   transition values and the values represent the human-readable labels.
   */
  public static function getHeaderPlacements():array {
    return [
      'start' => 'Start',
      'end' => 'End',
    ];
  }

  /**
   * Returns an array of modal close button placements.
   *
   * @return array
   *   An array of modal close button placements, where the keys represent the
   *   transition values and the values represent the human-readable labels.
   */
  public static function getCloseButtonPlacements():array {
    return [
      '' => 'None',
      'start' => 'Start',
      'end' => 'End',
      'start-out' => 'Start Outside',
      'end-out' => 'End Outside',
    ];
  }

  /**
   * Set preset.
   *
   * @param string $preset
   *   The preset.
   *
   * @return $this
   */
  public function setPreset(string $preset):self {
    $this->setSettingsVariationId($preset);
    return $this;
  }

  /**
   * Append to the closest selector.
   *
   * @param string $selector
   *   The selector.
   *
   * @return $this
   */
  public function setAppendTo(string $selector):self {
    $this->appendTo = $selector;
    return $this;
  }

  /**
   * Append to the closest selector.
   *
   * @param string $selector
   *   The selector.
   *
   * @return $this
   */
  public function setAppendToClosest(string $selector):self {
    $this->appendToClosest = $selector;
    return $this;
  }

  /**
   * Sets the color scheme of the modal.
   *
   * @param string $colorScheme
   *   The color scheme of the modal.
   *
   * @return $this
   */
  public function setColorScheme(string $colorScheme):self {
    if (substr($colorScheme, 0, 7) !== 'scheme-') {
      $colorScheme = 'scheme-' . $colorScheme;
    }
    $this->colorScheme = str_replace('_', '-', $colorScheme);
    return $this;
  }

  /**
   * Sets the width of the modal.
   *
   * @param string $value
   *   The width value.
   *
   * @return $this
   */
  public function setWidth(string $value):self {
    $this->width = $value;
    return $this;
  }

  /**
   * Sets the height of the modal.
   *
   * @param string $value
   *   The height value.
   *
   * @return $this
   */
  public function setHeight(string $value):self {
    $this->height = $value;
    return $this;
  }

  /**
   * Sets whether the modal has a backdrop.
   *
   * @param bool $backdrop
   *   TRUE if the modal has a backdrop, FALSE otherwise.
   *
   * @return $this
   */
  public function setBackdrop(bool $backdrop):self {
    $this->backdrop = $backdrop;
    return $this;
  }

  /**
   * Sets whether the modal has a header.
   *
   * @param bool $header
   *   TRUE if the modal has a header, FALSE otherwise.
   *
   * @return $this
   */
  public function setHeader(bool $header):self {
    $this->header = $header;
    return $this;
  }

  /**
   * Sets whether the modal header is in the content.
   *
   * @param bool $headerInContent
   *   TRUE if the modal header is in the content, FALSE otherwise.
   *
   * @return $this
   */
  public function setHeaderInContent(bool $headerInContent = TRUE):self {
    $this->headerInContent = $headerInContent;
    return $this;
  }

  /**
   * Sets whether the modal has a footer.
   *
   * @param bool $footer
   *   TRUE if the modal has a footer, FALSE otherwise.
   *
   * @return $this
   */
  public function setFooter(bool $footer):self {
    $this->footer = $footer;
    return $this;
  }

  /**
   * Sets whether the modal has a download link.
   *
   * @param bool $downloadLink
   *   TRUE if the modal has a download link, FALSE otherwise.
   *
   * @return $this
   */
  public function setDownloadLink(bool $downloadLink):self {
    $this->downloadLink = $downloadLink;
    return $this;
  }

  /**
   * Sets whether the modal has a share link.
   *
   * @param bool $shareLink
   *   TRUE if the modal has a share link, FALSE otherwise.
   *
   * @return $this
   */
  public function setShareLink(bool $shareLink):self {
    $this->shareLink = $shareLink;
    return $this;
  }

  /**
   * Sets whether the modal has a copy link.
   *
   * @param bool $copyLink
   *   TRUE if the modal has a copy link, FALSE otherwise.
   *
   * @return $this
   */
  public function setCopyLink(bool $copyLink):self {
    $this->copyLink = $copyLink;
    return $this;
  }

  /**
   * Sets the title of the modal.
   *
   * @param string $title
   *   The title of the modal.
   *
   * @return $this
   */
  public function setTitle(string $title):self {
    $this->title = $title;
    return $this;
  }

  /**
   * Sets the title of the modal with a dynamic icon.
   *
   * @param string $title
   *   The title of the modal.
   * @param array $prefix
   *   An array of icon prefixes.
   *
   * @return $this
   */
  public function setTitleWithDynamicIcon(string $title, $prefix = []):self {
    $this->title = $title;
    $icon = $this->loadIcon($title, NULL, NULL, $prefix);
    if ($icon) {
      $this->setRawIcon($icon->getSelector());
      $this->libraries[] = 'neo_modal/' . $icon->getLibrary()->getLibraryName();
    }
    return $this;
  }

  /**
   * Sets the subtitle of the modal.
   *
   * @param string $subtitle
   *   The subtitle of the modal.
   *
   * @return $this
   */
  public function setSubtitle(string $subtitle):self {
    $this->subtitle = $subtitle;
    return $this;
  }

  /**
   * Sets the icon of the modal.
   *
   * @param string $icon
   *   The icon of the modal. This is the full icon class.
   * @param array $prefix
   *   An array of icon prefixes.
   *
   * @return $this
   */
  public function setIcon(string $icon, $prefix = []):self {
    $icon = $this->loadIcon(NULL, $icon, NULL, $prefix);
    if ($icon) {
      $this->setRawIcon($icon->getSelector());
    }
    return $this;
  }

  /**
   * Sets the icon of the modal.
   *
   * @param string $icon
   *   The icon of the modal. This is the full icon class.
   *
   * @return $this
   */
  public function setRawIcon(string $icon):self {
    $this->icon = $icon;
    return $this;
  }

  /**
   * Sets the image of the modal.
   *
   * @param string $imageUrl
   *   The image of the modal.
   *
   * @return $this
   */
  public function setImage($imageUrl):self {
    $this->image = $imageUrl;
    return $this;
  }

  /**
   * Sets the video of the modal.
   *
   * @param string $videoUrl
   *   The video of the modal. Vimeo/YouTube/mp4/ogg/webm.
   *
   * @return $this
   */
  public function setVideo($videoUrl):self {
    $this->video = $videoUrl;
    return $this;
  }

  /**
   * Sets the iframe of the modal.
   *
   * @param string $iframeUrl
   *   The iframe of the modal.
   *
   * @return $this
   */
  public function setIframe($iframeUrl):self {
    $this->iframe = $iframeUrl;
    return $this;
  }

  /**
   * Sets the group of the modal.
   *
   * @param string $group
   *   The group of the modal.
   *
   * @return $this
   */
  public function setGroup($group):self {
    $this->group = $group;
    return $this;
  }

  /**
   * Sets whether the modal has smart actions.
   *
   * @param bool $smartActions
   *   TRUE if the modal has a smart actions, FALSE otherwise.
   *
   * @return $this
   */
  public function setSmartActions(bool $smartActions = TRUE):self {
    $this->smartActions = $smartActions;
    return $this;
  }

  /**
   * Sets whether the modal has numeration.
   *
   * @param bool $numeration
   *   TRUE if the modal has a numeration, FALSE otherwise.
   *
   * @return $this
   */
  public function setNumeration(bool $numeration = TRUE):self {
    $this->numeration = $numeration;
    return $this;
  }

  /**
   * Sets whether the modal is fit.
   *
   * Fit modals will try to fit the content to the screen.
   *
   * @param bool $fit
   *   TRUE if the modal has a fit, FALSE otherwise.
   *
   * @return $this
   */
  public function setFit(bool $fit = TRUE):self {
    $this->fit = $fit;
    return $this;
  }

  /**
   * Sets whether the modal is draggable.
   *
   * @param bool $drag
   *   TRUE if the modal is draggable, FALSE otherwise.
   *
   * @return $this
   */
  public function setDraggable(bool $drag = TRUE):self {
    $this->drag = $drag;
    return $this;
  }

  /**
   * Sets whether the body should be locked.
   *
   * @param bool $bodyLock
   *   TRUE if the body should be locked, FALSE otherwise.
   *
   * @return $this
   */
  public function setBodyLock(bool $bodyLock = TRUE):self {
    $this->bodyLock = $bodyLock;
    return $this;
  }

  /**
   * Sets the close button placement.
   *
   * @param string|bool $value
   *   The close button placement value. Can be 'start', 'end', 'start-out'
   *   or 'end-out'. If FALSE, the close button will not be displayed.
   *
   * @return $this
   */
  public function setCloseButton(string $value):self {
    if (in_array($value, array_keys(static::getCloseButtonPlacements()))) {
      $this->closeButton = $value;
    }
    return $this;
  }

  /**
   * Sets the content of the modal.
   *
   * @param array|string $content
   *   The content of the modal.
   *
   * @return $this
   */
  public function setContent($content):self {
    $this->content = $content;
    return $this;
  }

  /**
   * Sets the content of the modal to scroll.
   *
   * @param bool $contentScroll
   *   TRUE if the content should scroll, FALSE otherwise.
   *
   * @return $this
   */
  public function setContentScroll(bool $contentScroll = TRUE):self {
    $this->contentScroll = $contentScroll;
    return $this;
  }

  /**
   * Returns the content of the modal.
   *
   * @return mixed
   *   The content of the modal.
   */
  public function getContent():mixed {
    return $this->content;
  }

  /**
   * Sets the content padding of the modal.
   *
   * @param string $value
   *   The padding value.
   *
   * @return $this
   */
  public function setContentPadding(string $value):self {
    $this->contentPadding = $value;
    return $this;
  }

  /**
   * Returns the placement of the modal.
   *
   * @return string
   *   The placement value.
   */
  public function getPlacement():string {
    return $this->placement ?: $this->getSettings()->getValue('placement');
  }

  /**
   * Sets the placement of the modal.
   *
   * @param string $value
   *   The placement value.
   *
   * @return $this
   */
  public function setPlacement(string $value):self {
    if (isset(static::getPlacements()[$value])) {
      $this->placement = $value;
    }
    return $this;
  }

  /**
   * Sets the placement to center.
   *
   * @return $this
   */
  public function setPlacementToCenter():self {
    return $this->setPlacement('center');
  }

  /**
   * Sets the placement to top.
   *
   * @return $this
   */
  public function setPlacementToTop():self {
    return $this->setPlacement('top');
  }

  /**
   * Sets the placement to top start.
   *
   * @return $this
   */
  public function setPlacementToTopStart():self {
    return $this->setPlacement('top-start');
  }

  /**
   * Sets the placement to top end.
   *
   * @return $this
   */
  public function setPlacementToTopEnd():self {
    return $this->setPlacement('top-end');
  }

  /**
   * Sets the placement to bottom.
   *
   * @return $this
   */
  public function setPlacementToBottom():self {
    return $this->setPlacement('bottom');
  }

  /**
   * Sets the placement to bottom start.
   *
   * @return $this
   */
  public function setPlacementToBottomStart():self {
    return $this->setPlacement('bottom-start');
  }

  /**
   * Sets the placement to bottom end.
   *
   * @return $this
   */
  public function setPlacementToBottomEnd():self {
    return $this->setPlacement('bottom-end');
  }

  /**
   * Sets the placement to right.
   *
   * @return $this
   */
  public function setPlacementToRight():self {
    return $this->setPlacement('right');
  }

  /**
   * Sets the placement to right start.
   *
   * @return $this
   */
  public function setPlacementToRightStart():self {
    return $this->setPlacement('right-start');
  }

  /**
   * Sets the placement to right end.
   *
   * @return $this
   */
  public function setPlacementToRightEnd():self {
    return $this->setPlacement('right-end');
  }

  /**
   * Sets the placement to left.
   *
   * @return $this
   */
  public function setPlacementToLeft():self {
    return $this->setPlacement('left');
  }

  /**
   * Sets the placement to left start.
   *
   * @return $this
   */
  public function setPlacementToLeftStart():self {
    return $this->setPlacement('left-start');
  }

  /**
   * Sets the placement to left end.
   *
   * @return $this
   */
  public function setPlacementToLeftEnd():self {
    return $this->setPlacement('left-end');
  }

  /**
   * Sets the element to attach the modal to.
   *
   * @param string $selector
   *   The selector. Example: h1.
   *
   * @return $this
   */
  public function setAttach(string $selector):self {
    $this->attach = $selector;
    return $this;
  }

  /**
   * Sets the attach placement of the modal.
   *
   * @param string $value
   *   The placement value.
   *
   * @return $this
   */
  public function setAttachPlacement(string $value):self {
    if (isset(static::getAttachPlacements()[$value])) {
      $this->attachPlacement = $value;
    }
    return $this;
  }

  /**
   * Sets the attach placement to auto.
   *
   * @return $this
   */
  public function setAttachPlacementToAuto():self {
    return $this->setAttachPlacement('auto');
  }

  /**
   * Sets the attach placement to auto start.
   *
   * @return $this
   */
  public function setAttachPlacementToAutoStart():self {
    return $this->setAttachPlacement('auto-start');
  }

  /**
   * Sets the attach placement to top.
   *
   * @return $this
   */
  public function setAttachPlacementToTop(): self {
    return $this->setAttachPlacement('top');
  }

  /**
   * Sets the attach placement to top start.
   *
   * @return $this
   */
  public function setAttachPlacementToTopStart(): self {
    return $this->setAttachPlacement('top-start');
  }

  /**
   * Sets the attach placement to top end.
   *
   * @return $this
   */
  public function setAttachPlacementToTopEnd(): self {
    return $this->setAttachPlacement('top-end');
  }

  /**
   * Sets the attach placement to bottom.
   *
   * @return $this
   */
  public function setAttachPlacementToBottom(): self {
    return $this->setAttachPlacement('bottom');
  }

  /**
   * Sets the attach placement to bottom start.
   *
   * @return $this
   */
  public function setAttachPlacementToBottomStart(): self {
    return $this->setAttachPlacement('bottom-start');
  }

  /**
   * Sets the attach placement to bottom end.
   *
   * @return $this
   */
  public function setAttachPlacementToBottomEnd(): self {
    return $this->setAttachPlacement('bottom-end');
  }

  /**
   * Sets the attach placement to right.
   *
   * @return $this
   */
  public function setAttachPlacementToRight(): self {
    return $this->setAttachPlacement('right');
  }

  /**
   * Sets the attach placement to right start.
   *
   * @return $this
   */
  public function setAttachPlacementToRightStart(): self {
    return $this->setAttachPlacement('right-start');
  }

  /**
   * Sets the attach placement to right end.
   *
   * @return $this
   */
  public function setAttachPlacementToRightEnd(): self {
    return $this->setAttachPlacement('right-end');
  }

  /**
   * Sets the attach placement to left.
   *
   * @return $this
   */
  public function setAttachPlacementToLeft(): self {
    return $this->setAttachPlacement('left');
  }

  /**
   * Sets the attach placement to left start.
   *
   * @return $this
   */
  public function setAttachPlacementToLeftStart(): self {
    return $this->setAttachPlacement('left-start');
  }

  /**
   * Sets the attach placement to left end.
   *
   * @return $this
   */
  public function setAttachPlacementToLeftEnd(): self {
    return $this->setAttachPlacement('left-end');
  }

  /**
   * Sets the body transition scale.
   *
   * @param bool $bodyTransitionScale
   *   TRUE if the body should transition scale, FALSE otherwise.
   *
   * @return $this
   */
  public function setBodyTransitionScale(bool $bodyTransitionScale = TRUE):self {
    $this->bodyTransitionScale = $bodyTransitionScale;
    return $this;
  }

  /**
   * Sets the body transition blur.
   *
   * @param bool $bodyTransitionBlur
   *   TRUE if the body should transition blur, FALSE otherwise.
   *
   * @return $this
   */
  public function setBodyTransitionBlur(bool $bodyTransitionBlur = TRUE):self {
    $this->bodyTransitionBlur = $bodyTransitionBlur;
    return $this;
  }

  /**
   * Sets the content animation in.
   *
   * @param string $value
   *   The animation value.
   *
   * @return $this
   */
  public function setContentAnimateIn(string $value):self {
    if (isset(static::getAnimationsIn()[$value])) {
      $this->contentAnimateIn = $value;
    }
    return $this;
  }

  /**
   * Sets the content animation in speed.
   *
   * @param string $value
   *   The speed value.
   *
   * @return $this
   */
  public function setContentAnimateInSpeed(string $value):self {
    if (isset(static::getAnimationsSpeed()[$value])) {
      $this->contentAnimateInSpeed = $value;
    }
    return $this;
  }

  /**
   * Sets the content animation in delay.
   *
   * @param string $value
   *   The delay value.
   *
   * @return $this
   */
  public function setContentAnimateInDelay(string $value):self {
    if (isset(static::getAnimationsDelay()[$value])) {
      $this->contentAnimateInDelay = $value;
    }
    return $this;
  }

  /**
   * Sets the content animation out.
   *
   * @param string $value
   *   The animation value.
   *
   * @return $this
   */
  public function setContentAnimationOut(string $value):self {
    if (isset(static::getAnimationsOut()[$value])) {
      $this->contentAnimationOut = $value;
    }
    return $this;
  }

  /**
   * Sets the content animation out speed.
   *
   * @param string $value
   *   The speed value.
   *
   * @return $this
   */
  public function setContentAnimateOutSpeed(string $value):self {
    if (isset(static::getAnimationsSpeed()[$value])) {
      $this->contentAnimateOutSpeed = $value;
    }
    return $this;
  }

  /**
   * Sets the content animation out delay.
   *
   * @param string $value
   *   The delay value.
   *
   * @return $this
   */
  public function setContentAnimateOutDelay(string $value):self {
    if (isset(static::getAnimationsDelay()[$value])) {
      $this->contentAnimateOutDelay = $value;
    }
    return $this;
  }

  /**
   * Merge trigger attributes.
   *
   * @param array $attributes
   *   An associative array of key-value pairs to be converted to attributes.
   */
  public function mergeTriggerAttributes($attributes = []):self {
    $attributes = new Attribute($attributes);
    $this->triggerAttributes->merge($attributes);
    return $this;
  }

  /**
   * Add trigger class.
   */
  public function addTriggerClass():self {
    $args = func_get_args();
    $this->triggerAttributes->addClass($args);
    return $this;
  }

  /**
   * Set the content color.
   *
   * @param string $color
   *   The color.
   *
   * @return $this
   */
  public function setContentColor(string $color):self {
    $this->contentColor = $color;
    return $this;
  }

  /**
   * Set the content background color.
   *
   * @param string $color
   *   The color.
   *
   * @return $this
   */
  public function setContentColorBg(string $color):self {
    $this->contentColorBg = $color;
    return $this;
  }

  /**
   * Set the content footer color.
   *
   * @param string $color
   *   The color.
   *
   * @return $this
   */
  public function setContentFooterColor(string $color):self {
    $this->contentFooterColor = $color;
    return $this;
  }

  /**
   * Set the content footer background color.
   *
   * @param string $color
   *   The color.
   *
   * @return $this
   */
  public function setContentFooterColorBg(string $color):self {
    $this->contentFooterColorBg = $color;
    return $this;
  }

  /**
   * Set the header color.
   *
   * @param string $color
   *   The color.
   *
   * @return $this
   */
  public function setHeaderColor(string $color):self {
    $this->headerColor = $color;
    return $this;
  }

  /**
   * Set the header background color.
   *
   * @param string $color
   *   The color.
   *
   * @return $this
   */
  public function setHeaderColorBg(string $color):self {
    $this->headerColorBg = $color;
    return $this;
  }

  /**
   * Set the footer color.
   *
   * @param string $color
   *   The color.
   *
   * @return $this
   */
  public function setFooterColor(string $color):self {
    $this->footerColor = $color;
    return $this;
  }

  /**
   * Set the footer background color.
   *
   * @param string $color
   *   The color.
   *
   * @return $this
   */
  public function setFooterColorBg(string $color):self {
    $this->footerColorBg = $color;
    return $this;
  }

  /**
   * Set the navigation color.
   *
   * @param string $color
   *   The color.
   *
   * @return $this
   */
  public function setNavColor(string $color):self {
    $this->navColor = $color;
    return $this;
  }

  /**
   * Set the navigation background color.
   *
   * @param string $color
   *   The color.
   *
   * @return $this
   */
  public function setNavColorBg(string $color):self {
    $this->navColorBg = $color;
    return $this;
  }

  /**
   * Sets the backdrop color.
   *
   * @param string $color
   *   The backdrop color.
   *
   * @return $this
   */
  public function setBackdropColor(string $color):self {
    $this->backdropColorBg = $color;
    return $this;
  }

  /**
   * Set the navigation color.
   *
   * @param string $color
   *   The color.
   *
   * @return $this
   */
  public function setLoaderColor(string $color):self {
    $this->loaderColor = $color;
    return $this;
  }

  /**
   * Set the navigation background color.
   *
   * @param string $color
   *   The color.
   *
   * @return $this
   */
  public function setLoaderColorBg(string $color):self {
    $this->loaderColorBg = $color;
    return $this;
  }

  /**
   * Returns the attributes for the modal trigger.
   *
   * @return \Drupal\Core\Template\Attribute
   *   The attributes.
   */
  public function getTriggerAttributes():Attribute {
    $attributes = $this->triggerAttributes;

    if ($this->appendToClosest && $this->appendToClosest !== $this->getSettings()->getDiffConfigValue('appendToClosest')) {
      $attributes->setAttribute('data-neo-modal-appendToClosest', $this->appendToClosest);
    }
    elseif ($this->appendTo && $this->appendTo !== $this->getSettings()->getDiffConfigValue('appendTo')) {
      $attributes->setAttribute('data-neo-modal-appendTo', $this->appendTo);
    }
    if ($this->attach && $this->attach !== $this->getSettings()->getDiffConfigValue('attach')) {
      $attributes->setAttribute('data-neo-modal-attach', $this->attach);
      if ($this->attachPlacement && $this->attachPlacement !== $this->getSettings()->getDiffConfigValue('attachPlacement')) {
        $attributes->setAttribute('data-neo-modal-attachPlacement', $this->attachPlacement);
      }
    }
    if (!is_null($this->header) && (bool) $this->header !== (bool) $this->getSettings()->getDiffConfigValue('header', TRUE)) {
      $attributes->setAttribute('data-neo-modal-header', (bool) $this->header ? 'true' : 'false');
    }
    elseif (!is_null($this->headerInContent) && (bool) $this->headerInContent !== (bool) $this->getSettings()->getDiffConfigValue('headerInContent', FALSE)) {
      $attributes->setAttribute('data-neo-modal-headerInContent', (bool) $this->headerInContent ? 'true' : 'false');
    }
    // String options.
    foreach ([
      'colorScheme',
      'width',
      'height',
      'title',
      'subtitle',
      'icon',
      'image',
      'video',
      'iframe',
      'contentPadding',
      'placement',
      'group',
      'closeButton',
      'contentColor',
      'contentColorBg',
      'contentFooterColor',
      'contentFooterColorBg',
      'headerColor',
      'headerColorBg',
      'footerColor',
      'footerColorBg',
      'backdropColorBg',
      'navColor',
      'navColorBg',
      'loaderColor',
      'loaderColorBg',
      'contentAnimateIn',
      'contentAnimateInSpeed',
      'contentAnimateInDelay',
      'contentAnimationOut',
      'contentAnimateOutSpeed',
      'contentAnimateOutDelay',
    ] as $key) {
      if (!is_null($this->$key) && $this->$key !== $this->getSettings()->getDiffConfigValue($key)) {
        $attributes->setAttribute('data-neo-modal-' . $key, $this->$key);
      }
    }
    // Boolean options.
    foreach ([
      'backdrop' => TRUE,
      'footer' => TRUE,
      'drag' => TRUE,
      'contentScroll' => FALSE,
      'smartActions' => FALSE,
      'numeration' => FALSE,
      'fit' => FALSE,
      'bodyLock' => TRUE,
      'downloadLink' => TRUE,
      'shareLink' => TRUE,
      'copyLink' => TRUE,
      'bodyTransitionScale' => FALSE,
      'bodyTransitionBlur' => FALSE,
    ] as $key => $default) {
      if (!is_null($this->$key) && (bool) $this->$key !== (bool) $this->getSettings()->getDiffConfigValue($key, $default)) {
        $attributes->setAttribute('data-neo-modal-' . $key, (bool) $this->$key ? 'true' : 'false');
      }
    }
    return new Attribute($attributes);
  }

  /**
   * Returns the attributes for the modal content.
   *
   * @return \Drupal\Core\Template\Attribute
   *   The attributes.
   */
  public function getModalAttributes():Attribute {
    $attributes = [];
    $attributes['class'][] = 'neo-modal-template';
    return new Attribute($attributes);
  }

  /**
   * Returns the attachments for the modal.
   *
   * @return array
   *   The attachments.
   */
  public function getAttachments():array {
    $attachments = [];
    $attachments['library'][] = 'neo_modal/modal';
    foreach ($this->libraries as $library) {
      $attachments['library'][] = $library;
    }
    if ($globalOptions = $this->getSettings()->getDiffConfig()) {
      $attachments['drupalSettings']['neoModal']['defaults'] = $globalOptions;
    }
    return $attachments;
  }

  /**
   * Set the trigger overlay.
   *
   * @param string $title
   *   The title of the overlay.
   * @param string $icon
   *   The icon of the overlay.
   * @param bool $iconOnly
   *   TRUE if the overlay has an icon only, FALSE otherwise.
   * @param array $attributes
   *   The attributes of the overlay.
   *
   * @return $this
   */
  public function setTriggerOverlay($title, $icon = NULL, $iconOnly = FALSE, $attributes = []):self {
    $this->triggerOverlay = $title;
    $this->triggerOverlayIcon = $icon;
    if ($iconOnly) {
      $attributes['class'][] = 'neo-modal--trigger-overlay-icon-only';
    }
    $this->triggerOverlayAttributes = $attributes;
    return $this;
  }

  /**
   * Prepare the build.
   *
   * @param string|array $build
   *   The renderable array.
   */
  protected function buildTrigger($build) {
    if (is_string($build) || $build instanceof MarkupInterface) {
      $build = [
        '#markup' => $build,
      ];
    }
    if (empty($build['#type']) || in_array($build['#type'], [
      'markup',
      'plain_text',
    ]) || !empty($build['#markup'])) {
      $attributes = $build['#attributes'] ?? [];
      $attributes['href'] = '';
      $attributes['onclick'] = 'return false;';
      $build = [
        '#type' => 'html_tag',
        '#tag' => 'a',
        '#attributes' => $attributes,
        'value' => $build,
      ];
    }
    if ($this->triggerOverlay && isset($build['#type'])) {
      $build['#attributes']['class'][] = 'has-neo-modal-trigger-overlay';
      $overlay = [
        '#theme' => 'neo_modal_trigger_overlay',
        '#title' => $this->triggerOverlay,
        '#attributes' => $this->triggerOverlayAttributes,
      ];
      if ($this->triggerOverlayIcon) {
        if ($icon = $this->loadIcon(NULL, $this->triggerOverlayIcon)) {
          $overlay['#icon'] = $icon->render();
          $this->libraries[] = 'neo_modal/' . $icon->getLibrary()->getLibraryName();
        }
      }
      switch ($build['#type']) {
        case 'link':
          $build['#title'] = [
            'title' => $build['#title'],
            'overlay' => $overlay,
          ];
          break;

        default:
          $build['overlay'] = $overlay;
          break;
      }
    }
    $build['#attributes'] = $build['#attributes'] ?? [];
    $build['#attributes']['class'][] = 'neo-modal--trigger';
    $attribute = new Attribute($build['#attributes']);
    $attribute->merge($this->getTriggerAttributes());
    $build['#attributes'] = $attribute->toArray();
    foreach ($this->getAttachments() as $attachmentType => $attachments) {
      foreach ($attachments as $key => $attachment) {
        $build['#attached'][$attachmentType][$key] = $attachment;
      }
    }
    return $build;
  }

  /**
   * Build the content.
   *
   * @param string|array $build
   *   The modal content.
   * @param string $tag
   *   The tag. If empty, will be 'template'.
   *
   * @return array
   *   The renderable array.
   */
  public function buildContent(mixed $build, $tag = 'template'):array {
    if (is_string($build) || $build instanceof MarkupInterface) {
      $build = [
        '#type' => 'markup',
        '#markup' => $build,
      ];
    }
    $build['#prefix'] = Markup::create('<' . $tag . (string) $this->getModalAttributes() . '>');
    $build['#suffix'] = Markup::create('</' . $tag . '>');
    return $build;
  }

  /**
   * Apply the modal to a renderable array.
   *
   * @param string|array $build
   *   The renderable array.
   */
  public function applyTo(mixed &$build):void {
    $build = $this->buildTrigger($build);
    $build = [
      'trigger' => $build,
    ];
    if (!empty($this->content)) {
      $build['modal'] = $this->buildContent($this->content);
    }
  }

  /**
   * Apply the modal to a elements within a form.
   *
   * @param string|array $trigger
   *   The trigger renderable array.
   * @param array $content
   *   The content renderable array.
   */
  public function applyToForm(mixed &$trigger, array &$content):void {
    $this->setAppendToClosest('form');
    $trigger = $this->buildTrigger($trigger);
    $content = $this->buildContent($content, 'div');
  }

}
