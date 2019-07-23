<?php

namespace Drupal\payment\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Render\FormattableMarkup;

/**
 * AJAX example wizard.
 */
class PaymentForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'ajax_example_wizard';
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        // Since FormBase uses service traits, we can inject these services without
        // adding our own __construct() method.
        $form = new static($container);
        $form->setStringTranslation($container->get('string_translation'));
        $form->setMessenger($container->get('messenger'));
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $no_js_use = FALSE) {

        // We want to deal with hierarchical form values.
        $form['#tree'] = TRUE;
        $form['portletstart'] = [
            '#markup' => '<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
              <div class="kt-portlet">
                <div class="kt-portlet__body kt-portlet__body--fit">
                  <div class="kt-grid kt-wizard-v3 kt-wizard-v3--white" id="kt_wizard_v3" data-ktwizard-state="step-first">'
        ];
        $form['navigationsstart'] = [
            '#markup' => '<div class="kt-grid__item"><div class="kt-wizard-v3__nav">
                        <div class="kt-wizard-v3__nav-items">',
        ];

        switch ($form_state->getValue('step')) {
            case 1:
                $step1 = 'current';
                break;

            case 2:
                $step2 = 'current';
                break;

            case 3:
                $step3 = 'current';
                break;

            case 4:
                $step4 = 'current';
                break;
            
            default:
                $step1 = 'current';
                break;
        }




        $form['stepone'] = [
            '#markup' => '<div class="kt-wizard-v3__nav-label">
                                <span>1</span> Select Service
                              </div>
                              <div class="kt-wizard-v3__nav-bar"></div> ',
            '#prefix' => '<a class="kt-wizard-v3__nav-item" href="#" data-ktwizard-type="step" data-ktwizard-state="' . $step1 . '">
      <div class="kt-wizard-v3__nav-body">',
            '#suffix' => '</div></a>'
        ];

        $form['steptwo'] = [
            '#markup' => '<div class="kt-wizard-v3__nav-label"><span>2</span> Choose Service
                     </div><div class="kt-wizard-v3__nav-bar"></div> ',
            '#prefix' => '<a class="kt-wizard-v3__nav-item" href="#" data-ktwizard-type="step" data-ktwizard-state="' . $step2 . '">
      <div class="kt-wizard-v3__nav-body">',
            '#suffix' => '</div></a>'
        ];

        $form['stepthree'] = [
            '#markup' => '<div class="kt-wizard-v3__nav-label">
                                <span>3</span> Enter Details
                              </div>
                              <div class="kt-wizard-v3__nav-bar"></div> ',
            '#prefix' => '<a class="kt-wizard-v3__nav-item" href="#" data-ktwizard-type="step" data-ktwizard-state="' . $step3 . '">
      <div class="kt-wizard-v3__nav-body">',
            '#suffix' => '</div></a>'
        ];

        $form['stepfour'] = [
            '#markup' => '<div class="kt-wizard-v3__nav-label">
                                <span>4</span> Make Payment
                              </div>
                              <div class="kt-wizard-v3__nav-bar"></div> ',
            '#prefix' => '<a class="kt-wizard-v3__nav-item" href="#" data-ktwizard-type="step" data-ktwizard-state="' . $step4 . '">
        <div class="kt-wizard-v3__nav-body">',
            '#suffix' => '</div></a>'
        ];

        $form['navigationsend'] = [
            '#markup' => '</div></div></div>',
        ];

        $form['formstart'] = [
            '#markup' => '<div class="kt-grid__item kt-grid__item--fluid kt-wizard-v3__wrapper">

                      <!--begin: Form Wizard Form-->
                      <form class="kt-form" id="kt_form"><div class="kt-wizard-v3__content" data-ktwizard-type="step-content" data-ktwizard-state="current"><div class="kt-form__section kt-form__section--first">
                            <div class="kt-wizard-v3__form">',
        ];

        $form['step'] = [
            '#type' => 'value',
            '#value' => !empty($form_state->getValue('step')) ? $form_state->getValue('step') : 1,
        ];

        switch ($form['step']['#value']) {
            case 1:
                $limit_validation_errors = [['step']];
                $form['step1'] = [
                    '#type' => 'fieldset',
                    '#title' => $this->t('Step 1: Personal details'),
                ];
                $form['step1']['name'] = [
                    '#type' => 'textfield',
                    '#title' => $this->t('Your name'),
                    '#default_value' => $form_state->hasValue(['step1', 'name']) ? $form_state->getValue(['step1', 'name']) : '',
                    '#required' => TRUE,
                ];
                $form['step1']['email'] = [
                    '#type' => 'textfield',
                    '#title' => $this->t('Your Email'),
                    '#default_value' => $form_state->hasValue(['step1', 'email']) ? $form_state->getValue(['step1', 'email']) : '',
                    '#required' => TRUE,
                ];

                break;

            case 2:
                $limit_validation_errors = [['step'], ['step1']];
                $form['step1'] = [
                    '#type' => 'value',
                    '#value' => $form_state->getValue('step1'),
                ];
                $form['step2'] = [
                    '#type' => 'fieldset',
                    '#title' => t('Step 2: Street address info'),
                ];
                $form['step2']['address'] = [
                    '#type' => 'textfield',
                    '#title' => $this->t('Your street address'),
                    '#default_value' => $form_state->hasValue(['step2', 'address']) ? $form_state->getValue(['step2', 'address']) : '',
                    '#required' => TRUE,
                ];
                break;

            case 3:
                $limit_validation_errors = [['step'], ['step1'], ['step2']];
                $form['step1'] = [
                    '#type' => 'value',
                    '#value' => $form_state->getValue('step1'),
                ];
                $form['step2'] = [
                    '#type' => 'value',
                    '#value' => $form_state->getValue('step2'),
                ];
                $form['step3'] = [
                    '#type' => 'fieldset',
                    '#title' => $this->t('Step 3: City info'),
                ];
                $form['step3']['city'] = [
                    '#type' => 'textfield',
                    '#title' => $this->t('Your city'),
                    '#default_value' => $form_state->hasValue(['step3', 'city']) ? $form_state->getValue(['step3', 'city']) : '',
                    '#required' => TRUE,
                ];
                $form['step3']['street'] = [
                    '#type' => 'textfield',
                    '#title' => $this->t('Street'),
                    '#default_value' => $form_state->hasValue(['step3', 'street']) ? $form_state->getValue(['step3', 'street']) : '',
                    '#required' => TRUE,
                ];
                $form['step3']['zip'] = [
                    '#type' => 'textfield',
                    '#title' => $this->t('Postal Code'),
                    '#default_value' => $form_state->hasValue(['step3', 'zip']) ? $form_state->getValue(['step3', 'zip']) : '',
                    '#required' => TRUE,
                ];
              \Stripe\Stripe::setApiKey($stripe['secret_key']);
                $publishable_key = $stripe['publishable_key'];
                $stripe_email = 'anaraj.pedde@gmail.com';//$_SESSION['stripe_email']; //Give your mail id
                $amount = 100; //Give your  amount to be transfer
                $stripe_amount = $amount * 100;
                $form['step3']['pay_button'] = [
                    '#markup' =>
                    new FormattableMarkup('<p><script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
 data-key="' . $stripe['publishable_key'] . '" data-amount="' . $stripe_amount . '" data-description="User Registration"data-currency="EUR" data-email="' . $stripe_email . '">
  </script>
  </p>
  ', []),
                ];
                
                break;
        }

        $form['actions'] = ['#type' => 'actions'];
        if ($form['step']['#value'] > 1) {
            $form['actions']['prev'] = [
                '#type' => 'submit',
                '#value' => $this->t('Previous step'),
                '#limit_validation_errors' => $limit_validation_errors,
                '#submit' => ['::prevSubmit'],
                '#ajax' => [
                    'wrapper' => 'ajax-example-wizard-wrapper',
                    'callback' => '::prompt',
                ],
            ];
        }
        if ($form['step']['#value'] != 3) {
            $form['actions']['next'] = [
                '#type' => 'submit',
                '#value' => $this->t('Next step'),
                '#submit' => ['::nextSubmit'],
                '#ajax' => [
                    'wrapper' => 'ajax-example-wizard-wrapper',
                    'callback' => '::prompt',
                ],
            ];
        }
        if ($form['step']['#value'] == 3) {
            $form['actions']['submit'] = [
                '#type' => 'submit',
                '#value' => $this->t("Submit your information"),
            ];
        }


        // This simply allows us to demonstrate no-javascript use without
        // actually turning off javascript in the browser. Removing the #ajax
        // element turns off AJAX behaviors on that element and as a result
        // ajax.js doesn't get loaded.
        // For demonstration only! You don't need this.
        if ($no_js_use) {
            // Remove the #ajax from the above, so ajax.js won't be loaded.
            // For demonstration only.
            unset($form['actions']['next']['#ajax']);
            unset($form['actions']['prev']['#ajax']);
        }

        $form['#prefix'] = '<div id="ajax-example-wizard-wrapper">';
        $form['#suffix'] = '</div>';

        $form['formend'] = [
            '#markup' => '</div></div></div></form></div>',
        ];

        $form['portletend'] = [
            '#markup' => '</div></div></div></div>',
        ];
        return $form;
    }

    /**
     * Wizard callback function.
     *
     * @param array $form
     *   Form API form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   Form API form.
     *
     * @return array
     *   Form array.
     */
    public function prompt(array $form, FormStateInterface $form_state) {
        return $form;
    }

    /**
     * Ajax callback that moves the form to the next step and rebuild the form.
     *
     * @param array $form
     *   The Form API form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The FormState object.
     *
     * @return array
     *   The Form API form.
     */
    public function nextSubmit(array $form, FormStateInterface $form_state) {
        $form_state->setValue('step', $form_state->getValue('step') + 1);
        $form_state->setRebuild();
        return $form;
    }

    /**
     * Ajax callback that moves the form to the previous step.
     *
     * @param array $form
     *   The Form API form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The FormState object.
     *
     * @return array
     *   The Form API form.
     */
    public function prevSubmit(array $form, FormStateInterface $form_state) {
        $form_state->setValue('step', $form_state->getValue('step') - 1);
        $form_state->setRebuild();
        return $form;
    }

    /**
     * Save away the current information.
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $messenger = $this->messenger();
        $messenger->addMessage($this->t('Your information has been submitted:'));
        $messenger->addMessage($this->t('Name: @name', ['@name' => $form_state->getValue(['step1', 'name'])]));
        $messenger->addMessage($this->t('Address: @address', ['@address' => $form_state->getValue(['step2', 'address'])]));
        $messenger->addMessage($this->t('City: @city', ['@city' => $form_state->getValue(['step3', 'city'])]));
    }

}
