<?php

/**
 * @file
 * Support tide_workflow_reviewer's views.
 */

/**
 * Implements hook_views_data_alter().
 */
function tide_workflow_reviewer_views_data_alter(array &$data) {
  $data['node_field_revision']['reviewer_view_field'] = [
    'title' => t('Content Reviewer'),
    'field' => [
      'title' => t('Content Reviewer'),
      'help' => t('site administrators can assign contents to other users for review'),
      'id' => 'reviewer_view_field',
    ],
  ];
  $data['node_field_revision']['workbench_reviewer'] = [
    'title' => t('Node Workbench Reviewer'),
    'field' => [
      'title' => t('Node Workbench Reviewer'),
      'help' => t('Specify a user that can be assigned as a reviewer of a node.'),
      'id' => 'assigned_user_view_field',
    ],
  ];
}
