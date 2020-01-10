<?php

namespace Drupal\tide_workflow_reviewer\Plugin\views\field;

use Drupal\Core\Url;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to reviewer_view_field.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("reviewer_view_field")
 */
class ReviewerViewField extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // No need.
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $node = $values->_entity;
    $options = [
      '#title' => 'Assign to ...',
      '#type' => 'link',
      '#url' => Url::fromRoute('tide_workflow_reviewer.reviewer_form', ['node' => $node->id()]),
      '#attributes' => [
        'class' => ['use-ajax'],
        'data-dialog-type' => 'dialog',
        'data-dialog-renderer' => 'off_canvas',
      ],
    ];
    return $options;
  }

}
