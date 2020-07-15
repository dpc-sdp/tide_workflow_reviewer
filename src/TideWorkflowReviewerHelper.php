<?php

namespace Drupal\tide_workflow_reviewer;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\node\NodeInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Helper class.
 */
class TideWorkflowReviewerHelper {

  /**
   * Returns a list of users who have access the provided node.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node passed in.
   *
   * @return array
   *   A list of users keyed by node id.
   */
  public static function getAssociatedUsers(NodeInterface $node): array {
    $user_storage = \Drupal::service('entity_type.manager')->getStorage('user');
    $query = $user_storage->getQuery();
    $condition = $query->orConditionGroup()
      ->condition('roles', 'approver', 'CONTAINS')
      ->condition('roles', 'editor', 'CONTAINS');
    $query->condition($condition);
    $result = $query->execute();
    $users = User::loadMultiple($result);
    $options = [];
    $options['empty'] = '';
    foreach ($users as $user) {
      $options[$user->id()] = $user->label();
    }
    return $options;
  }

  /**
   * Update the node with the workbench_reviewer field.
   */
  public static function updatesAssignedUserWithNode(NodeInterface $node, UserInterface $user, string $message): void {
    $node->workbench_reviewer->entity = $user;
    $node->revision_log = $message;
    $node->save();
  }

  /**
   * Sending email.
   */
  public static function mail(NodeInterface $node, User $user, array $extras):void {
    global $base_url;
    $mail_manager = \Drupal::service('plugin.manager.mail');
    /** @var Drupal\Core\Messenger\MessengerInterface $messenger */
    $messenger = \Drupal::service('messenger');
    $key = 'tide_workflow_reviewer_email';
    $module_name = 'tide_workflow_reviewer';
    $email = $user->mail->value;
    $params['title_of_content'] = $node->label();
    $params['node_link'] = $base_url . $node->toUrl()->toString();
    $params['message'] = $extras['message'];
    $langcode = $user->getPreferredLangcode();
    $send = TRUE;
    $result = $mail_manager->mail($module_name, $key, $email, $langcode, $params, NULL, $send);
    if ($result['result'] !== TRUE) {
      $messenger->addMessage(t('There was a problem sending your email notifications and it was not sent.'), MessengerInterface::TYPE_ERROR);
    }
    else {
      $messenger->addMessage(t('Your email notifications has been sent.'), MessengerInterface::TYPE_STATUS);
    }
  }

  /**
   * Helper function for checking if the user has roles.
   *
   * @param array $roles
   *   Eg. ['site_admin', 'administrator'].
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The user entity.
   *
   * @return bool
   *   returns TRUE means
   *   that at lease 1 role matches the role that the user has.
   */
  public static function userHasRoles(array $roles, AccountInterface $user): bool {
    $result = FALSE;
    if (count(array_intersect($user->getRoles(), $roles)) > 0) {
      $result = TRUE;
    }
    return $result;
  }

  /**
   * Helper function.
   *
   * This is for checking if the user who has ['site_admin', 'administrator']
   * roles and checking if the user is viewing the node view.
   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   Current User.
   * @param array $roles
   *   User roles.
   * @param array $routes
   *   Routes.
   *
   * @return bool
   *   Returns True or False.
   */
  public static function areRolesOnRoutes(AccountInterface $user, array $roles, array $routes): bool {
    $result = FALSE;
    $route_name = \Drupal::routeMatch()->getRouteName();
    if (in_array($route_name, $routes) && static::userHasRoles($roles, $user)) {
      $result = TRUE;
    }
    return $result;
  }

  /**
   * Helper function.
   *
   * A helper function for checking whether the current user has right
   * permission in right router.This function is only for this module.
   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The current user.
   *
   * @return bool
   *   Returns
   */
  public static function hasPermissionToReviewerWidget(AccountInterface $user): bool {
    // Only checking two routes.
    $valid_routes = [
      'entity.node.latest_version',
      'entity.node.canonical',
    ];
    $roles = ['site_admin', 'administrator'];

    // Returns true if the current user with the roles listed above.
    if (static::areRolesOnRoutes($user, $roles, $valid_routes)) {
      return TRUE;
    }

    $node = \Drupal::routeMatch()->getParameter('node');
    // Returns true if workbench reviewer is the current user.
    if ($node instanceof NodeInterface && !$node->workbench_reviewer->isEmpty() && $node->workbench_reviewer->entity->id() == $user->id()) {
      return TRUE;
    }
    return FALSE;
  }

}
