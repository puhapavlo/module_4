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

    $x = 0;
    for ($i = 0; $i <= $x; $i++) {
      $form["table"][$i]["Year"] = [
        '#type' => 'textfield',
        "#default_value" => "2022",
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

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }

}
