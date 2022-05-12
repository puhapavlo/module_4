<?php

namespace Drupal\pablo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @file
 * Contains \Drupal\pablo\Form\TableForm.
 */

/**
 * Provides form for the pablo module.
 */
class TableForm extends FormBase {

  /**
   * Number of tables.
   *
   * @var int
   */
  protected $tables = 1;

  /**
   * Number of rows.
   *
   * @var array
   */
  protected $rows = [1];

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
    // Add a wrapper for ajax update.
    $form["#prefix"] = "<div id='table-wrapper'>";
    $form["#suffix"] = "</div>";
    // Call constructor for table.
    $this->createTable($form, $form_state);

    $form["addTable"] = [
      "#type" => "submit",
      "#value" => $this->t("Add Table"),
      "#submit" => ["::addTable"],
      "#ajax" => [
        "event" => "click",
        "progress" => [
          "type" => "throbber",
        ],
        "callback" => "::ajaxRefresh",
        "wrapper" => "table-wrapper",
      ],
      "#attributes" => [
        "class" => [
          "table-btn",
        ],
      ],
    ];

    $form["submit"] = [
      "#type" => "submit",
      "#name" => "submit",
      "#value" => $this->t("Submit"),
      "#ajax" => [
        "event" => "click",
        "progress" => [
          "type" => "throbber",
        ],
        "callback" => "::ajaxRefresh",
        "wrapper" => "table-wrapper",
      ],
      "#attributes" => [
        "class" => [
          "table-btn",
        ],
      ],
    ];

    return $form;
  }

  /**
   * Constructor for table.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function createTable(array &$form, FormStateInterface $form_state) {
    // Header for table.
    $headerTable = [
      "year" => $this->t("Year"),
      "jan" => $this->t("Jan"),
      "feb" => $this->t("Feb"),
      "mar" => $this->t("Mar"),
      "q1" => $this->t("Q1"),
      "apr" => $this->t("Apr"),
      "may" => $this->t("May"),
      "jun" => $this->t("Jun"),
      "q2" => $this->t("Q2"),
      "jul" => $this->t("Jul"),
      "aug" => $this->t("Aug"),
      "sep" => $this->t("Sep"),
      "q3" => $this->t("Q3"),
      "oct" => $this->t("Oct"),
      "nov" => $this->t("Nov"),
      "dec" => $this->t("Dec"),
      "q4" => $this->t("Q4"),
      "ytd" => $this->t("YTD"),
    ];

    // Create a number for tables.
    for ($i = 0; $i < $this->tables; $i++) {
      $form["addRow_$i"] = [
        "#type" => "submit",
        "#value" => "Add row",
        "#submit" => ["::addRow"],
        "#name" => $i,
        "#ajax" => [
          "event" => "click",
          "progress" => [
            "type" => "throbber",
          ],
          "callback" => "::ajaxRefresh",
          "wrapper" => "table-wrapper",
        ],
        "#attributes" => [
          "class" => [
            "table-btn",
          ],
        ],
      ];

      $form["table_$i"] = [
        "#type" => "table",
        "#header" => $headerTable,
        "#attributes" => [
          "class" => [
            "table",
          ],
        ],
      ];

      // Create a number for rows.
      for ($t = $this->rows[$i]; $t > 0; $t--) {
        foreach ($headerTable as $header) {
          $form["table_$i"]["rows_$t"]["$header"] = [
            "#type" => "number",
            "#attributes" => [
              "class" => [
                "table-input",
              ],
            ],
          ];

          if (in_array("$header", ["Q1", "Q2", "Q3", "Q4", "YTD"])) {
            $form["table_$i"]["rows_$t"]["$header"] = [
              "#type" => "number",
              "#disabled" => TRUE,
              "#attributes" => [
                "class" => [
                  "table-input",
                ],
              ],
            ];
          }
        }

        $form["table_$i"]["rows_$t"]["Year"] = [
          "#type" => "number",
          "#disabled" => TRUE,
          "#default_value" => date("Y") - $t,
          "#attributes" => [
            "class" => [
              "table-input",
            ],
          ],
        ];
      }
    }

    $form["#attached"]["library"][] = "pablo/global";
    return $form;
  }

  /**
   * Function for adding a table.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function addTable(array &$form, FormStateInterface $form_state) {
    $this->tables++;
    // Set 1 row for new table.
    $this->rows[] = 1;
    $form_state->setRebuild();
    return $form;
  }

  /**
   * Function for adding row.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function addRow(array $form, FormStateInterface $form_state) {
    $i = $form_state->getTriggeringElement()["#name"];
    $this->rows[$i]++;
    $form_state->setRebuild();
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validate only when press the button submit.
    if ($form_state->getTriggeringElement()["#name"] !== "submit") {
      return;
    }

    $tablesValues = $form_state->getValues();
    $minRow = array_search(min($this->rows), $this->rows);

    for ($i = 0; $i < $this->tables; $i++) {
      $hasValue = FALSE;
      $hasEmpty = FALSE;

      // Cycle for tables.
      for ($t = 1; $t <= $this->rows[$i]; $t++) {
        foreach (array_reverse($tablesValues["table_$i"]["rows_$t"]) as $key => $k) {
          if (in_array("$key", ["Year", "Q1", "Q2", "Q3", "Q4", "YTD"])) {
            goto end;
          }

          if ($t <= $this->rows[$minRow]) {
            if (!$hasValue && !$hasEmpty && $k !== "") {
              $hasValue = TRUE;
            }

            if ($hasValue && !$hasEmpty && $k == "") {
              $hasEmpty = TRUE;
            }

            if ($hasValue && $hasEmpty && $k !== "") {
              $form_state->setErrorByName("Invalid", "Invalid");
            }

            if ($tablesValues["table_$minRow"]["rows_$t"][$key] == "" && $k !== "" ||
              $tablesValues["table_$minRow"]["rows_$t"][$key] !== "" && $k == "") {
              $form_state->setErrorByName("Invalid", "Invalid");
            }
          }

          elseif ($k !== "") {
            $form_state->setErrorByName("Invalid", "Invalid");
          }
          end:
        }
      }
      if (!$hasValue && !$hasEmpty) {
        $form_state->setErrorByName("Invalid", "Invalid");
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getErrors()) {
      $form_state->clearErrors();
      $this->messenger()->addStatus("Invalid");
    }
    else {
      // Loop for all tables.
      for ($i = 0; $i < $this->tables; $i++) {
        for ($t = 0; $t <= $this->rows[$i]; $t++) {
          $value = $form_state->getValue(["table_$i", "rows_$t"]);
          $q1 = $q2 = $q3 = $q4 = 0;
          if ($value["Jan"] != "" || $value["Feb"] != "" || $value["Mar"] != "") {
            $q1 = (int) $value["Jan"] + (int) $value["Feb"] + (int) $value["Mar"];
            $q1 = round(($q1 + 1) / 3, 2);
            $form["table_$i"]["rows_$t"]["Q1"]["#value"] = $q1;
          }
          if ($value["Apr"] != "" || $value["May"] != "" || $value["Jun"] != "") {
            $q2 = (int) $value["Apr"] + (int) $value["May"] + (int) $value["Jun"];
            $q2 = round(($q2 + 1) / 3, 2);
            $form["table_$i"]["rows_$t"]["Q2"]["#value"] = $q2;
          }
          if ($value["Jul"] != "" || $value["Aug"] != "" || $value["Sep"] != "") {
            $q3 = (int) $value["Jul"] + (int) $value["Aug"] + (int) $value["Sep"];
            $q3 = round(($q3 + 1) / 3, 2);
            $form["table_$i"]["rows_$t"]["Q3"]["#value"] = $q3;
          }
          if ($value["Oct"] != "" || $value["Nov"] != "" || $value["Dec"] != "") {
            $q4 = (int) $value["Oct"] + (int) $value["Nov"] + (int) $value["Dec"];
            $q4 = round(($q4 + 1) / 3, 2);
            $form["table_$i"]["rows_$t"]["Q4"]["#value"] = $q4;
          }
          if ($q1 || $q2 || $q3 || $q4) {
            $ytd = $q1 + $q2 + $q3 + $q4;
            $ytd = round(($ytd + 1) / 4, 2);
            $form["table_$i"]["rows_$t"]["YTD"]["#value"] = $ytd;
          }
        }
      }
      $this->messenger()->addStatus("Valid");
    }
  }

  /**
   * Function for ajax refresh.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function ajaxRefresh(array $form, FormStateInterface $form_state) {
    return $form;
  }

}
