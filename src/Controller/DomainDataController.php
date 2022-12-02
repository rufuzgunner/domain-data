<?php

namespace Drupal\domain_data\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Domain Data routes.
 */
class DomainDataController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Domain data include info'),
    ];

    return $build;
  }

}
