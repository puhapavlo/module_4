<?php

namespace Drupal\pablo\Form;

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
    $num = $form_state->get("num");
    if (empty($num)) {
      $num = 0;
      $form_state->set("num", $num);
    }

    $form["actions"]["addRow"] = [
      "#type" => "submit",
      "#value" => $this->t("Add row"),
      "#submit" => ["::addRowCallback"],
    ];

    $form["table"] = [
      "#type" => "table",
      "#header" => [
        "Year",
        "Jan",
        "Feb",
        "Mar",
        "Q1",
        "Apr",
        "May",
        "Jun",
        "Q2",
        "Jul",
        "Aug",
        "Sep",
        "Q3",
        "Oct",
        "Nov",
        "Dec",
        "Q4",
        "YTD",
      ],
    ];
    for ($i = 0; $i <= $num; $i++) {
      $form["table"][$i]["Year"] = [
        '#type' => 'textfield',
        "#value" => 2022 - $i,
      ];

      $form["table"][$i]["YTD"] = $form["table"][$i]["Q4"] = $form["table"][$i]["Dec"] = $form["table"][$i]["Nov"] = $form["table"][$i]["Oct"] =
      $form["table"][$i]["Q3"] = $form["table"][$i]["Sep"] = $form["table"][$i]["Aug"] = $form["table"][$i]["Jul"] = $form["table"][$i]["Q2"] =
      $form["table"][$i]["Jun"] = $form["table"][$i]["May"] = $form["table"][$i]["Apr"] = $form["table"][$i]["Q1"] = $form["table"][$i]["Mar"] =
      $form["table"][$i]["Feb"] = $form["table"][$i]["Jan"] = [
        "#type" => "textfield",
      ];

      $form["table"][$i]["Year"]["#attributes"] = $form["table"][$i]["Q1"]["#attributes"] = $form["table"][$i]["Q2"]["#attributes"] = $form["table"][$i]["Q3"]["#attributes"] =
      $form["table"][$i]["Q4"]["#attributes"] = $form["table"][$i]["YTD"]["#attributes"] = [
        "readonly" => "readonly",
      ];
    }

    $form["actions"]["submit"] = [
      "#type" => "submit",
      "#value" => $this->t("Submit"),
    ];

    return $form;
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
    $num = $form_state->get("num");
    $num++;
    $form_state->set("num", $num);
    $form_state->setRebuild();
  }

}
