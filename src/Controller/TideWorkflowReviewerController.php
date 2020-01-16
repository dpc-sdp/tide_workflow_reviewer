<?php

namespace Drupal\tide_workflow_reviewer\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\tide_workflow_reviewer\TideWorkflowReviewerHelper;

/**
 * Access checker class is only for checking a simple logic.
 *
 * Class TideWorkflowReviewerController.
 *
 * @package Drupal\tide_workflow_reviewer\Controller
 */
class TideWorkflowReviewerController {

  /**
   * Checking if the user has site_admin role.
   */
  public function access(AccountInterface $account) {
    return AccessResult::allowedIf(TideWorkflowReviewerHelper::userHasRoles([
      'site_admin',
      'administrator',
    ], $account));
  }

}
