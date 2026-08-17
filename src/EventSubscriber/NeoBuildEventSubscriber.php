<?php

declare(strict_types=1);

namespace Drupal\neo_modal\EventSubscriber;

use Drupal\neo_build\Event\NeoBuildEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Contributes the modal's Tailwind variant and spacing scale to the Neo build.
 */
class NeoBuildEventSubscriber implements EventSubscriberInterface {

  /**
   * Subscribe to the Neo build event dispatched.
   *
   * @param \Drupal\neo_build\Event\NeoBuildEvent $event
   *   The neo build event.
   */
  public function onBuild(NeoBuildEvent $event) {
    $collection = $event->getCollection();
    $collection->addTailwindVariants([
      'modal' => ['.neo-modal &'],
    ]);

    $theme = [];
    foreach ([
      't', 'r', 'b', 'l',
    ] as $pos) {
      $theme['extend']['spacing']['modal-' . $pos] = 'var(--modal-' . $pos . ', 0px)';
      $theme['extend']['spacing']['modal-content-' . $pos] = 'var(--modal-content-' . $pos . ', 0px)';
    }
    $collection->addTailwindTheme($theme);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      NeoBuildEvent::EVENT_NAME => 'onBuild',
    ];
  }

}
