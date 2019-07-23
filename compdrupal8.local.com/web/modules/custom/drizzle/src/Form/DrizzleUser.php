<?php

namespace Drupal\drizzle\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\customutil\CustomUtils;
use Drupal\drizzle\Controller\DrizzleController;
use Drupal\user\Entity\Role;

class DrizzleUser extends FormBase {

    public $tpk;

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'drizzleuser_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state, $tpk = '', $formmode = '') {
        $this->formmode = $formmode;
        $this->tpk = $tpk;

        $dritems = DrizzleController::drizzle_load_from_db($tpk);

        $max = $form_state->get('fields_count');
        $role_objects = Role::loadMultiple();
        $system_roles = array_combine(array_keys($role_objects), array_map(function($a) {
                    return $a->label();
                }, $role_objects));



        $form['#prefix'] = '<div id="sponsorship-form-wrapper">';
        $form['#suffix'] = '</div>';

        $form['sponsor'] = [
            '#markup' => '',
//            '#title' => $this->t(''),
            '#prefix' => '<form role="form" class="kt-form">
                        <div class="form-body">
                        <div class="row">',
            '#suffix' => '</div></div></form><hr/>',
        ];


        $form['sponsor']['submit'] = [
            '#type' => 'submit',
            '#prefix' => '<i class="la la-save"></i>',
            '#value' => t('Save'),
            '#attributes' => [
                'class' => ['btn btn-full']
            ],
            '#ajax' => array(
                'wrapper' => 'sponsorship-form-wrapper',
                'callback' => '::ajaxRebuildForm',
                'effect' => 'fade',
                'progress' => array('message' => '', 'type' => 'throbber'),
            ),
        ];
        $form['sponsor']['cancel'] = [
            '#markup' => CustomUtils::cancelButton('drizzle.drizzlemenu', $idarray, 'extrasmall', 'Cancel')
        ];

        $form['sponsor']['drizzleinfo'] = $this->drizzle_header_form($dritems);



        $form['items'] = [
            '#type' => 'container',
            '#tree' => TRUE,
            '#prefix' => '<div id="duser-fieldset-wrapper">',
            '#suffix' => '</div>',
        ];

        $form['items']['add_duser'] = [
            '#type' => 'submit',
            '#prefix' => '<span class="containPlus">+</span>',
            '#suffix' => '<div><br></div>',
            '#attributes' => [
                'class' => ['btn-add-duser']
            ],
            '#value' => t('Add a User'),
            '#submit' => array('::addDuser'),
            '#ajax' => [
                'callback' => '::addDuserCallback',
                'wrapper' => 'duser-fieldset-wrapper',
            ],
            '#name' => 'addfield',
        ];

        if (($this->formmode == 'EDIT') || ($this->formmode == 'DISPLAY')) {
            if (is_null($max)) {

                foreach ($dritems['items'] as $k => $val) {

                    //if removed item is not in items then only add the elements to the Max Array
                    if (!in_array($val['tupk'], $form_state->get('deletecounter'))) {
                        $max[$k] = $k;
                    }
                }

                $form_state->set('fields_count', $max);
            } else {

                //unsetting the values of items if its removed form Form item
                foreach ($form_state->get('deletecounter') as $k => $v) {
                    unset($dritems['items'][$k]);
                }

                // Set item count 1 if all items removed form form
                if (empty($max) || is_null($max)) {
                    $max = array(0 => 0);
                    $form_state->set('fields_count', $max);
                }
            }


            foreach ($max as $delta => $value) {
                $dbaction = $form_state->getValue(['items', $delta, 'dbaction']);
                $dbact = isset($dbaction) ? $dbaction : (!empty($dritems['items'][$delta]['tupk']) ? 'u' : 'i');
                $dritems['items'][$delta]['dbaction'] = $dbact;
                $form['items'][$delta] = $this->users_item_form($delta, $dritems['items'][$delta], $system_roles);
            }
        } else {
            if (count($max) <= 1) {
                $max = array(0 => 0);
                $form_state->set('fields_count', $max);
            }
//            print_r($max);
            foreach ($max as $delta => $value) {
                $form['items'][$delta] = $this->users_item_form($delta, array(), $system_roles);
            }
        }


        return $form;
    }

    public function drizzle_header_form($dritems, $display_mode = NULL) {

        global $user;


        $uname = \Drupal::currentUser()->getUsername();
        $query = db_select('tfrttenant', 't');
        $query->innerjoin('tfrttenantuser', 'tu', 't.tpk = tu.tpk');
        $query->fields('t', array('tenant', 'tpk'));
        $tenantdet = $query->execute()->fetchAssoc();

        $form['wrapper'] = [
            '#markup' => '',
            '#prefix' => '<div class="row">',
            '#suffix' => '</div>',
        ];

        $form['wrapper']['tpk'] = array(
            '#type' => 'hidden',
            '#title' => t('Tpk'),
            '#default_value' => isset($dritems['tpk']) ? $tenantdet['tpk'] : '',
        );


        if (isset($display_mode)) {
            // $form['tenant']['#value'] = $fritems['tenant'];
        }

        $form['wrapper']['tname'] = array(
            '#type' => isset($display_mode) ? 'item' : 'textfield',
            '#title' => t('Name'),
            '#default_value' => isset($dritems['tname']) ? $dritems['tname'] : '',
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        );

        if (isset($display_mode)) {
            $form['tname']['#value'] = $dritems['tname'];
        }
        $form['wrapper']['company'] = array(
            '#type' => isset($display_mode) ? 'item' : 'textfield',
            '#title' => t('Company Name'),
            '#default_value' => isset($dritems['company']) ? $dritems['company'] : '',
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        );

        if (isset($display_mode)) {
            $form['company']['#value'] = $dritems['company'];
        }

        $form['wrapper']['twebsite'] = array(
            '#type' => isset($display_mode) ? 'item' : 'textfield',
            '#title' => t('Website'),
            '#default_value' => isset($dritems['twebsite']) ? $dritems['twebsite'] : '',
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        );

        if (isset($display_mode)) {
            $form['wrapper']['twebsite']['#value'] = $fritems['twebsite'];
        }
        $form['#attributes'] = array('enctype' => 'multipart/form-data');

//        $tlogos = drizzle_getarray('');
//        $form['tlogo'] = array(
//            '#type' => isset($display_mode) ? 'item' : 'file',
//            '#title' => t('Logo'),
//            '#default_value' => isset($fritems['tlogo']) ? $fritems['tlogo'] : '',
//            '#attributes' => array('size' => '25')
//        );

        if (isset($display_mode)) {
            $form['wrapper']['tlogo']['#value'] = $fritems['tlogo'];
        }

        if ($formmode == 'EDIT') {
            $form['tlogo']['#type'] = 'hidden';
            $form['wrapper']['shtlogo'] = array(
                '#type' => 'item',
                '#title' => t('Logo'),
                '#value' => $dritems['tlogo'],
                '#prefix' => '<div class="col-md-6">',
                '#suffix' => '</div>'
            );
        }

        $date = date('Y-m-d');
        $form['wrapper']['tstartdate'] = array(
            '#type' => isset($display_mode) ? 'item' : 'date',
            '#title' => t('Start date'),
//            '#date_format' => 'Y-m-d',
            '#default_value' => isset($dritems['tstartdate']) ? $dritems['tstartdate'] : date('Y-m-d'),
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        );

        if (isset($display_mode)) {
            $form['wrapper']['tstartdate']['#value'] = date('d-m-Y', strtotime($fritems['tstartdate']));
        }

        $date = date('Y-m-d');
        $form['wrapper']['tenddate'] = array(
            '#type' => isset($display_mode) ? 'item' : 'date',
            '#title' => t('End date'),
//            '#date_format' => 'd-m-Y',
            '#default_value' => isset($fritems['tenddate']) ? $fritems['tenddate'] : date('Y-m-d', strtotime("365 days")),
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        );
        if (isset($display_mode)) {
            $form['wrapper']['tenddate']['#value'] = date('d-m-Y', strtotime($fritems['tenddate']));
        }

        $form['wrapper']['tnoofusers'] = array(
            '#type' => (\Drupal::currentUser()->hasPermission('make golive')) ? isset($display_mode) ? 'item' : 'textfield' : 'hidden',
            '#title' => t('No. of Users they can add'),
            '#default_value' => isset($fritems['tnoofusers']) ? $fritems['tnoofusers'] : '',
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        );
        if (isset($display_mode)) {
            $form['tnoofusers']['#value'] = $fritems['tnoofusers'];
        }
//        if ($formmode == 'EDIT')
//            $tenant_details = db_fetch_array(db_query("select * from tfrttenant where tpk = %d", arg(2)));



//        $tstatuss = drizzle_getarray('TNST');
        $form['wrapper']['tstatus'] = array(
            '#type' => isset($display_mode) ? 'item' : 'select',
            '#title' => t('Status'),
            '#options' => $tstatuss,
            '#default_value' => isset($fritems['tstatus']) ? $fritems['tstatus'] : '',
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>'
        );

        if (isset($display_mode)) {
            $form['tstatus']['#value'] = $fritems['tstatus'];
        }
        
        $form['wrapper']['tgolive'] = array(
            '#type' => (\Drupal::currentUser()->hasPermission('make golive')) ? isset($display_mode) ? 'radios' : 'radios' : 'hidden',
            //'#type' => 'checkbox',
            '#title' => t('Can go live'),
            '#validate' => true,
            '#options' => array('Y' => 'Yes', 'N' => 'No'),
            '#default_value' => isset($fritems['tgolive']) ? $fritems['tgolive'] : '',
            '#prefix' => '<div class="col-md-6"><div class="kt-radio-inline">',
            '#suffix' => '</div></div>',
//            '#attributes' => [
//                'class' => ['kt-radio']
//            ],
            '#label_attributes' => ['class' => ['kt-radio']],
        );

//        if ($formmode == 'EDIT' && $tenant_details['livestatus'] == 'LIVE') {
//
//            $form['tgolive']['#attributes'] = array('disabled' => 'disabled');
//        }

        if ($display_mode) {
            $form['tgolive']['#attributes'] = array('disabled' => 'disabled');
        }        

        return $form;
    }

    public function users_item_form($i, $details, $roles) {

        $form['items'][$i] = [
            '#markup' => '',
            '#value' => $this->t('Your sponsee'),
            '#prefix' => '<div class="row">',
            '#suffix' => '</div><hr>',
        ];

        $form['items'][$i]['tpk'] = array(
            '#type' => 'hidden',
            '#default_value' => isset($details['tpk']) ? $details['tpk'] : null,
        );

        $form['items'][$i]['tupk'] = array(
            '#type' => 'hidden',
            '#default_value' => isset($details['tupk']) ? $details['tupk'] : null,
            '#attributes' => array('class' => 'short')
        );

        $form['items'][$i]['tuname'] = array(
            '#type' => isset($display_mode) ? 'item' : 'textfield',
            '#title' => $this->t('Name'),
            '#default_value' => isset($details['tuname']) ? $details['tuname'] : '',
            '#title_display' => 'invisible',
            '#placeholder' => $this->t('user Name'),
            '#prefix' => '<div class="col-md-2">',
            '#suffix' => '</div>',
        );

        $form['items'][$i]['e_mail'] = [
            '#type' => 'email',
            '#title' => $this->t('E-mail'),
            '#placeholder' => $this->t('E-mail'),
            '#default_value' => isset($details['tuname']) ? $details['tuname'] : '',
            '#title_display' => 'invisible',
            '#prefix' => '<div class="col-md-2">',
            '#suffix' => '</div>',
        ];
        $tustatuss = array('active' => 'Active', 'sentinv' => 'Send Invitation');
        $form['items'][$i]['tustatus'] = array(
            '#type' => isset($display_mode) ? 'item' : 'select',
            '#options' => $tustatuss,
            '#title' => $this->t('Status'),
            '#title_display' => 'invisible',
            '#default_value' => isset($details['tustatus']) ? $details['tustatus'] : '',
            '#prefix' => '<div class="col-md-2">',
            '#suffix' => '</div>',
        );
        $form['items'][$i]['roles'] = array(
            '#type' => 'checkboxes',
            '#title' => t(''),
//        '#multicolumn' => array('width' => 2, 'row-major' => TRUE,),
            '#options' => $roles,
            '#default_value' => isset($details['roles']) ? $details['roles'] : (isset($rols) ? $rols : array()),
            '#prefix' => '<div class="col-md-3"><div class="kt-checkbox-list">',
            '#suffix' => '</div></div>',
        );

        $form['items'][$i]['dbaction'] = array(
            '#type' => 'hidden',
            '#default_value' => isset($details['dbaction']) ? $details['dbaction'] : '',
        );
        $form['items'][$i]['remove'] = [
            '#type' => 'submit',
            '#idx' => $i,
            '#name' => "REMOVE" . $i,
            '#removepk' => $details['tupk'],
            '#value' => t('-'),
            '#submit' => array('::removeCallback'),
            '#ajax' => [
                'callback' => '::removefieldCallback',
                'wrapper' => 'duser-fieldset-wrapper',
            ],
            '#limit_validation_errors' => TRUE,
            '#prefix' => '<div class="col-md-2 col-sm-10">',
            '#suffix' => '</div>'
        ];
        return $form;
    }

    public function removefieldCallback(array &$form, FormStateInterface &$form_state) {
        return $form['items'];
    }

    public function removeCallback(array &$form, FormStateInterface $form_state) {
        $button_clicked = $form_state->getTriggeringElement();
        $idx = substr($button_clicked['#name'], 6);
        $removepk = $button_clicked['#removepk'];
        $max = $form_state->get('fields_count');
        unset($max[$idx]);
        unset($form['items'][$idx]);
        $form_state->set('fields_count', $max);

        if (!empty($removepk)) {
            $form_state->set(['deletecounter', $idx], $removepk);
        }

        $form_state->setRebuild(TRUE);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $form_state->set('fields_count', 1);
        // Display result.
        foreach ($form_state->getValues() as $key => $value) {
            //drupal_set_message($key . ': ' . $value);
        }
        foreach ($form_state->get('deletecounter') as $key => $v) {
            drupal_set_message($key . ': ' . $v);
        }
    }

    /**
     * Ajax submit handler that will return the whole form structure.
     *  = callback of the complete submit of the form
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     *
     * @return array
     *   The form structure.
     */
    public function ajaxRebuildForm(array &$form, FormStateInterface $form_state) {
        return $form;
    }

    /**
     * Callback for both ajax-enabled buttons.
     *
     * Selects and returns the fieldset with the names in it.
     */
    public function addDuserCallback(array &$form, FormStateInterface $form_state) {
        return $form['items'];
    }

    /**
     * Submit handler for the "add-one-more" button.
     *
     * Increments the max counter and causes a rebuild.
     */
    public function addDuser(array &$form, FormStateInterface $form_state) {
//        $form_state->set('fields_count', $form_state->get('fields_count') + 1);
        //$form_state->setRebuild();
        $button_clicked = $form_state->getTriggeringElement();
        $idx = substr($button_clicked['#name'], 0, 5);

        if ($idx != 'REMOVE') {
            $max = $form_state->get('fields_count');
            $count = end($max);
            array_push($max, $count + 1);
//            print_r($max); 
            $form_state->set('fields_count', $max);
            $form_state->setValue(['items', $count + 1, 'dbaction'], 'i');
            $form_state->setRebuild(TRUE);
        } else {
            
        }
    }

}
