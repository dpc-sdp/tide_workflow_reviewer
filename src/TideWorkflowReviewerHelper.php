<?php

namespace Drupal\tide_workflow_reviewer;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\node\NodeInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

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
  public function getAssociatedUsers(NodeInterface $node): array {
    $users = User::loadMultiple();
    $options = [];
    $options['empty'] = '';
    foreach ($users as $user) {
      if ($node->access('update', $user)) {
        $options[$user->id()] = $user->label();
      }
    }
    return $options;
  }

  /**
   * Update the node with the workbench_reviewer field.
   */
  public function updatesAssignedUserWithNode(NodeInterface $node, UserInterface $user) {
    $node->workbench_reviewer->entity = $user;
    $node->save();
  }

  /**
   * Sending email.
   */
  public function mail(NodeInterface $node, User $user, array $extras) {
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

}
