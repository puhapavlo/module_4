<?php

namespace Drupal\pablo\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @file
 * Contains \Drupal\pablo\Form\TableForm.
 */

/**
 * Provides form for the guestbook module.
 */
class TableForm extends FormBase {

  protected int $rows = 1;

  protected int  $tables = 1;
  /**
   * {@inheritdoc}
   */
  public function getFormId() {

    return "table_form";
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form["#prefix"] = "<div id='table-wrapper'>";
    $form["#suffix"] = "</div>";

    $this->createTable($form, $form_state);

    $form["addTable"] = [
      "#type" => 'submit',
      "#value" => $this->t("Add Table"),
      "#submit" => ["::addTable"],
    ];

    $form["actions"]["submit"] = [
      "#type" => "submit",
      "#value" => $this->t("Submit"),
    ];

    return $form;
  }

  public function createTable(array &$form, FormStateInterface $form_state) {
    $headerTable = [
      'year' => $this->t('Year'),
      'jan' => $this->t('Jan'),
      'feb' => $this->t('Feb'),
      'mar' => $this->t('Mar'),
      'q1' => $this->t('Q1'),
      'apr' => $this->t('Apr'),
      'may' => $this->t('May'),
      'jun' => $this->t('Jun'),
      'q2' => $this->t('Q2'),
      'jul' => $this->t('Jul'),
      'aug' => $this->t('Aug'),
      'sep' => $this->t('Sep'),
      'q3' => $this->t('Q3'),
      'oct' => $this->t('Oct'),
      'nov' => $this->t('Nov'),
      'dec' => $this->t('Dec'),
      'q4' => $this->t('Q4'),
      'ytd' => $this->t('YTD'),
    ];

    for ($i = 0; $i < $this->tables; $i++) {
      $form["addRow"] = [
        "#type" => "submit",
        "#value" => "Add row",
        "#submit" => ["::addRow"],
      ];

      $form["table"] = [
        "#type" => "table",
        "#header" => $headerTable,
      ];

      for ($i = $this->rows; $i > 0; $i--) {
        foreach ($headerTable as $header) {
          $form["table"]["rows"]["$header"] = [
            "#type" => "number",
          ];

          if (in_array("$header", ["Q1", "Q2", "Q3", "Q4", "YTD"])) {
            $form["table"]["rows"]["$header"] = [
              "#type" => "number",
              "#disabled" => TRUE,
            ];
          }
        }

        $form["table"]["rows"]['Year'] = [
          '#type' => 'number',
          '#disabled' => TRUE,
          '#default_value' => date('Y') - $i + 1,
        ];
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage("Valid");
  }

  /**
   * Callback for button "Add row".
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function addRowCallback(array &$form, FormStateInterface $form_state) {
    return $form["wrapper"];
  }

}
