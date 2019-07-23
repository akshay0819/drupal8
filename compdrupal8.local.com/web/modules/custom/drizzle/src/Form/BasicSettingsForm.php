<?php

namespace Drupal\drizzle\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
/**
 * Description of BasicSettingsForm
 *
 * @author anaraj.pedde
 */
class BasicSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'customutil_basic_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['drizzle.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('drizzle.settings');

    $form['text_val'] = [
      '#type' => 'textfield',
      '#title' => $this->t('My Module setting'),
      '#default_value' => $config->get('text_val'),
    ];

    $form['dbconnection_database'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Database'),
      '#default_value' => $config->get('dbconnection.database'),
    ];

    $form['dbconnection_host'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Host'),
      '#default_value' => $config->get('dbconnection.host'),
    ];

    $form['dbconnection_port'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Port'),
      '#default_value' => $config->get('dbconnection.port'),
    ];

    $form['dbconnection_driver'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Driver'),
      '#default_value' => $config->get('dbconnection.driver'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $config = $this->config('drizzle.settings');
    $config->set('text_val', $form_state->getValue('text_val'));
    $config->set('dbconnection.database', $form_state->getValue('dbconnection_database'));
    $config->set('dbconnection.host', $form_state->getValue('dbconnection_host'));
    $config->set('dbconnection.port', $form_state->getValue('dbconnection_port'));
    $config->set('dbconnection.driver', $form_state->getValue('dbconnection_driver'));
    $config->save();
  }

}
