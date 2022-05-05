<?php

namespace Drupal\pablo\Controller;

/**
 * @file
 * Contains \Drupal\pablo\Controller\GuestbookController.
 *
 * @return
 */

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the pablo module.
 */
class PabloController extends ControllerBase {

  /**
   * Returns a page.
   *
   * @return array
   *   A renderable array.
   */
  public function content() {
    $tableForm = \Drupal::formBuilder()->getForm("Drupal\pablo\Form\TableForm");
    return [
      "#theme" => "pablo_template",
      "#form" => $tableForm,
    ];
  }

}
