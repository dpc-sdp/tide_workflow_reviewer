<?php

namespace Drupal\tide_workflow_reviewer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\NodeInterface;
use Drupal\tide_workflow_reviewer\TideWorkflowReviewerHelper;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A form for user selection and sending emails.
 */
class TideWorkflowReviewerForm extends FormBase {

  /**
   * Router match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * {@inheritdoc}
   */
  public function __construct(RouteMatchInterface $route_match) {
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'tide_workflow_reviewer_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL) {
    if (!$node) {
      $node = $this->routeMatch->getParameter('node');
    }

    $options = TideWorkflowReviewerHelper::getAssociatedUsers($node);
    $this->routeMatch->getParameter('node');
    $form['tide_workflow_reviewer']['options'] = [
      '#type' => 'select',
      '#title' => '<p>Assign</p>' . '<em>' . $node->label() . '</em>' . '<p>to</p>',
      '#chosen' => TRUE,
      '#options' => $options,
      '#multiple' => FALSE,
      '#default_value' => $node->workbench_reviewer->isEmpty() ? '' : $node->workbench_reviewer->entity->id(),
    ];

    $form['tide_workflow_reviewer']['message'] = [
      '#type' => 'textarea',
      '#title' => t('Message'),
      '#default_value' => $node->getRevisionLogMessage() ? $node->getRevisionLogMessage() : '',
    ];

    $form['tide_workflow_reviewer']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Send',
      '#submit' => [
        [$this, 'apply'],
      ],
    ];
    $form['#attached']['library'][] = 'tide_workflow_reviewer/tide-workflow-reviewer-form';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Keep it empty.
  }

  /**
   * Submit handler.
   */
  public function apply(array $form, FormStateInterface $form_state) {
    $user_id = $form_state->getValue('options');
    $message = $form_state->getValue('message');
    $user = User::load($user_id);
    $node = $this->routeMatch->getParameter('node');
    TideWorkflowReviewerHelper::updatesAssignedUserWithNode($node, $user, $message);
    TideWorkflowReviewerHelper::mail($node, $user, ['message' => $message]);
    $valid_routes = [
      'entity.node.latest_version',
      'entity.node.canonical',
    ];
    // Redirect the page is the user in view.tide_workflow_reviewer_view.page_1.
    if (!in_array($this->routeMatch->getRouteName(), $valid_routes)) {
      $form_state->setRedirect('view.tide_workflow_reviewer_view.page_1');
    }
  }

}
