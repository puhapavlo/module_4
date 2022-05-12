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

  protected int  $tables = 1;

  protected array $rows = [1];
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
      "#type" => "submit",
      "#value" => $this->t("Add Table"),
      "#submit" => ["::addTable"],
    ];

    $form["actions"]["submit"] = [
      "#type" => "submit",
      "#name" => "submit",
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
      $form["addRow_$i"] = [
        "#type" => "submit",
        "#value" => "Add row",
        "#submit" => ["::addRow"],
        "#name" => $i,
      ];

      $form["table_$i"] = [
        "#type" => "table",
        "#header" => $headerTable,
      ];

      for ($t = 0; $t < $this->rows[$i]; $t++) {
        foreach ($headerTable as $header) {
          $form["table_$i"]["rows_$t"]["$header"] = [
            "#type" => "number",
          ];

          if (in_array("$header", ["Q1", "Q2", "Q3", "Q4", "YTD"])) {
            $form["table_$i"]["rows_$t"]["$header"] = [
              "#type" => "number",
              "#disabled" => TRUE,
            ];
          }
        }

        $form["table_$i"]["rows_$t"]['Year'] = [
          '#type' => 'number',
          '#disabled' => TRUE,
          '#default_value' => date('Y') - $t,
        ];
      }
    }
    return $form;
  }

  public function addTable(array &$form, FormStateInterface $form_state) {
    $this->tables++;
    $this->rows[] = 1;
    $form_state->setRebuild();
    return $form;
  }
  public function addRow(array $form, FormStateInterface $form_state) {
    $i = $form_state->getTriggeringElement()['#name'];
    $this->rows[$i]++;
    $form_state->setRebuild();
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getTriggeringElement()["#name"] !== "submit") {
      return;
    }

    $tablesValues = $form_state->getValues();
    $minRow = array_search(min($this->rows), $this->rows);

    for ($i = 0; $i < $this->tables; $i++) {
      $hasValue = FALSE;
      $hasEmpty = FALSE;

      for ($t = 1; $t <= $this->rows[$i]; $t++) {
        foreach (array_reverse($tablesValues["table_$i"]["rows_$t"]) as $key => $k) {
          if ($t <= $this->rows[$minRow]) {
            if (!$hasValue && !$hasEmpty && $k !== "") {
              $hasValue = TRUE;
            }

            if ($hasValue && !$hasEmpty && $k == "") {
              $hasEmpty = TRUE;
            }

            if ($hasValue && $hasEmpty && $i !== "") {
              $form_state->setErrorByName('Invalid', "Invalid");
            }

            if ($tablesValues["table_$minRow"]["rows_$t"][$key] == "" && $i !== "" ||
              $tablesValues["table_$minRow"]["rows_$t"][$key] !== "" && $i == "") {
              $form_state->setErrorByName( 'Invalid', "Invalid");
            }
          }

          elseif ($i !== "") {
            $form_state->setErrorByName( 'Invalid', "Invalid");
          }
        }
        if (!$hasValue && !$hasEmpty) {
          $form_state->setErrorByName('Invalid', "Invalid");
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->messenger()->addStatus("Valid");
  }

  /**
   * Callback for button "Add row".
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function refreshAjax(array &$form, FormStateInterface $form_state) {
    return $form;
  }

}
