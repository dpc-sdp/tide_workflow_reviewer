<?php

namespace Drupal\tide_workflow_reviewer\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

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
    return AccessResult::allowedIf(count(array_intersect($account->getRoles(), ['site_admin', 'administrator'])) > 0);
  }

}
