<?php

declare(strict_types=1);

namespace Drupal\neo_modal\EventSubscriber;

use Drupal\neo_build\Event\NeoBuildEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UserLoginSubscriber.
 *
 * @package Drupal\custom_events\EventSubscriber
 */
class NeoBuildEventSubscriber implements EventSubscriberInterface {

  /**
   * Subscribe to the user login event dispatched.
   *
   * @param \Drupal\custom_events\Event\UserLoginEvent $event
   *   Our custom event object.
   */
  public function onBuild(NeoBuildEvent $event) {
    $config = $event->getConfig();
    $config['tailwind']['variants']['modal'] = ['.neo-modal &'];
    foreach ([
      't', 'r', 'b', 'l',
    ] as $pos) {
      $config['tailwind']['theme']['extend']['spacing']['modal-' . $pos] = 'var(--modal-' . $pos . ', 0px)';
      $config['tailwind']['theme']['extend']['spacing']['modal-content-' . $pos] = 'var(--modal-content-' . $pos . ', 0px)';
    }
    $event->setConfig($config);
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
