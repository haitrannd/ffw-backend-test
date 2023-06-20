<?php

namespace Drupal\site_settings\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

/**
 * Site-settings form.
 */
class PublishControlForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'publish_control_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $articleNodes = $this->getArticleNodes();

    $form['publish_control'] = array(
      '#type' => 'fieldset',
      '#title' => t('Control publishing options'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    );

    $form['publish_control']['node'] = array(
      '#type' => 'select',
      '#options' => $articleNodes,
    );

    $form['publish_control']['status'] = array(
      '#type' => 'select',
      '#options' => [
        1 => 'Published',
        0 => 'Unpublished',
      ],
    );

    $form['publish_control']['sticky'] = array(
      '#type' => 'select',
      '#options' => [
        1 => 'Sticky',
        0 => 'Unsticky',
      ],
    );

    $form['publish_control']['actions']['#type'] = 'actions';

    $form['publish_control']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
      '#submit' => [[$this, 'updateNode']],
      '#button_type' => 'primary',
    ];

    $form['publish_control']['actions']['delete'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete'),
      '#submit' => [[$this, 'deleteNode']],
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $nid = $form_state->getValue('node');
    if(!$nid) {
      $form_state->setErrorByName('node', $this->t('Please select article!'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * Update selected node
   */
  public function updateNode(array &$form, FormStateInterface $form_state) {
    $nid = $form_state->getValue('node');
    $publish = $form_state->getValue('status');
    $sticky = $form_state->getValue('sticky');
    $node = Node::load($nid);
    $node->set('status', $publish);
    $node->setSticky($sticky);
    $node->save();
    $this->messenger()->addStatus($this->t('Update successfully'));
  }

  /**
   * Delete selected node
   */
  public function deleteNode(array &$form, FormStateInterface $form_state) {
    $nid = $form_state->getValue('node');
    $node = Node::load($nid);
    $node->delete();
    $this->messenger()->addStatus($this->t('Delete successfully'));
  }

  /**
   * Get all article nodes
   */
  public function getArticleNodes() {
    $data = [];
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = $storage->getQuery()
      ->condition('type', 'article')
      ->execute();
    if (!empty($query)) {
      $nodes = $storage->loadMultiple($query);
      foreach($nodes as $node) {
        $data[$node->id()] = $node->title->value;
      }
    }
    return $data;
  }

}
