<?php

namespace Drupal\commentapi\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * A relatively simple AJAX demonstration form.
 */
class CommentsModule extends FormBase {

    public function getFormId() {
        return 'comments_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {// This container wil be replaced by AJAX.
        $form['container'] = [
            '#type' => 'container',
            '#attributes' => ['id' => 'box-container'],
        ];

        $query = db_select('commentsapi', 'comm');
        $query->fields('comm');
        $result = $query->execute()->fetchAll();
        $variab = '';
        foreach ($result as $item) {
            $variab = $variab . '<br/>' . $item->comment;
        }

        $renderable = [
            '#theme' => 'commentsview',
            '#title' => 'Comments',
            '#description' => $result,
        ];
        $rendered = \Drupal::service('renderer')->render($renderable);


        // The box contains some markup that we can change on a submit request.
        $form['container']['box'] = [
            '#type' => 'markup',
            '#markup' => $rendered,
        ];


        $form['container']['addcomments'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Your name'),
            '#default_value' => $form_state->hasValue(['container', 'name']) ? $form_state->getValue(['container', 'name']) : '',
            '#required' => TRUE,
        ];
        $form['submit'] = [
            '#type' => 'submit',
            // The AJAX handler will call our callback, and will replace whatever page
            // element has id box-container.
            '#ajax' => [
                'callback' => '::promptCallback',
                'wrapper' => 'box-container',
            ],
            '#value' => $this->t('Submit Comments'),
        ];

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        
    }

    public function promptCallback(array &$form, FormStateInterface $form_state) {

        global $base_url;
        $values = $form_state->getValues();
        $uid = \Drupal::currentUser()->id();
        //DbTransaction
        $transaction = db_transaction();

        $commentspk = db_insert('commentsapi')
                ->fields(array(
                    'comment' => $values['addcomments'],
                    'userid' => $uid,
                ))
                ->execute();

        $query = db_select('commentsapi', 'comm');
        $query->fields('comm');
        $result = $query->execute()->fetchAll();
        $variab = '';
        foreach ($result as $item) {
            $variab = $variab . '<br/>' . $item->comment;
        }


        $renderable = [
            '#theme' => 'commentsview',
            '#title' => 'Test Value',
            '#description' => $result,
        ];
        $rendered = \Drupal::service('renderer')->render($renderable);

        $element = $form['container'];
        $element['box']['#markup'] = $rendered;
        return $element;
    }

}
