<?php

namespace Drupal\tide_workflow_reviewer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\NodeInterface;
use Drupal\node\Entity\Node;
use Drupal\tide_workflow_reviewer\TideWorkflowReviewerHelper;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A form for user selection and sending emails.
 */
class TideWorkflowReviewerForm extends FormBase {


  /**
   * Helper.
   *
   * @var \Drupal\tide_workflow_reviewer\TideWorkflowReviewerHelper
   */
  protected $helper;

  /**
   * Router match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * {@inheritdoc}
   */
  public function __construct(TideWorkflowReviewerHelper $helper, RouteMatchInterface $route_match) {
    $this->helper = $helper;
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('tide_workflow_reviewer.helper'),
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
    if (!$node instanceof NodeInterface && $this->routeMatch->getRouteName() == 'entity.node.revision') {
      $node = Node::load($node);
    }
    $options = $this->helper->getAssociatedUsers($node);
    $this->routeMatch->getParameter('node');
    $form['tide_workflow_reviewer']['options'] = [
      '#type' => 'select',
      '#title' => $node->label() . ' Assigning to',
      '#chosen' => TRUE,
      '#options' => $options,
      '#multiple' => FALSE,
      '#default_value' => $node->workbench_reviewer->isEmpty() ? '' : $node->workbench_reviewer->entity->id(),
    ];

    $form['tide_workflow_reviewer']['message'] = [
      '#type' => 'textarea',
      '#title' => t('Message'),
    ];

    $form['tide_workflow_reviewer']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Send',
      '#submit' => [
        [$this, 'apply'],
      ],
    ];
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
    $user = User::load($user_id);
    $node = $this->routeMatch->getParameter('node');
    $this->helper->updatesAssignedUserWithNode($node, $user);
    $this->helper->mail($node, $user, ['message' => $form_state->getValue('message')]);
    if ($this->routeMatch->getRouteName() != 'entity.node.canonical') {
      $form_state->setRedirect('view.tide_workflow_reviewer_view.page_1');
    }
  }

}
