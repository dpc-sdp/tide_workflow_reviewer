<?php

namespace Drupal\tide_workflow_reviewer\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to Assigned User View Field.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("assigned_user_view_field")
 */
class AssignedUserViewField extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $node = $values->_entity;
    if ($node->workbench_reviewer->isEmpty()) {
      return $this->t('No user assigned');
    }
    return $node->workbench_reviewer->entity->label();
  }

}
