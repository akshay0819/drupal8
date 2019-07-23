<?php

/**
 * @file
 * Contains Drupal\ajax_example\AjaxExampleForm
 */

namespace Drupal\mymodule;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\mymodule\Mymodule_biz;
use Drupal\Core\Url;
use Drupal\Core\Link;

class Addcompanies extends FormBase {

    public $companypk;

    public function getFormId() {
        return 'ajax_example_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state, $companypk = '', $formmode = '') {


        $this->companypk = $companypk;
        $this->formmode = $formmode;

        $compdet = Mymodule_biz::getcompanydet($companypk);
        $form['submitactions'][] = $this->form_actions('up', $this->formmode);
        $form['companyinformation'] = [
            '#type' => 'vertical_tabs',
            '#default_tab' => 'edit-companyinfo',
        ];
        // 1St tab
        $form['companyinfo'] = [
            '#type' => 'details',
            '#title' => 'Company Information',
            '#group' => 'companyinformation',
        ];
        // 1St tab2
        $form['companyinfo2'] = [
            '#type' => 'details',
            '#title' => 'Company Information 2',
            '#group' => 'companyinformation',
        ];

        $form['companyinfo']['compinfo1'] = $this->compinfotab1($compdet);
        $form['companyinfo2']['compinfo2'] = $this->compinfotab2($compdet);



        //Ahah Form starts here   

        if ($this->formmode != 'DISPLAY') {
            $form['actions']['add'] = array(
                '#type' => 'submit',
                '#name' => 'addfield',
                '#value' => t('Add more Items'),
                '#submit' => array(array($this, 'addfieldsubmit')),
                '#ajax' => array(
                    'callback' => array($this, 'addfieldCallback'),
                    'wrapper' => 'items-wrapper',
                    'effect' => 'fade',
                ),
                '#limit_validation_errors' => TRUE,
            );
        }

        $max = $form_state->get('fields_count');

        $form['itemshead'] = array(
            '#type' => 'container',
            '#prefix' => '<div class="col-md-12">',
            '#suffix' => '</div>'
        );

        $form['itemshead']['itemheader'] = array(
            '#markup' => '<div class="col-md-5 col-sm-10">Empl Name</div><div class="col-md-5 col-sm-10">phone</div><div class="col-md-2 col-sm-10">Remove</div>',
            '#prefix' => '<div class="item-table-title">',
            '#suffix' => '</div>',
        );

        $form['items'] = array(
            '#type' => 'container',
            '#caption' => $this->t('Items'),
            '#attributes' => array(
                'class' => array('container-inline'),
            ),
            '#tree' => TRUE,
            '#prefix' => '<div id="items-wrapper">',
            '#suffix' => '</div>',
        );

        ////////////////
        //Items Level Form
        ///////////////
        if (($this->formmode == 'EDIT') || ($this->formmode == 'DISPLAY')) {
            if (is_null($max)) {
                foreach ($compdet['items'] as $k => $val) {
                    //if removed item is not in items then only add the elements to the Max Array
                    if (!in_array($val['companyitempk'], $form_state->get('deletecounter'))) {
                        $max[$k] = $k;
                    }
                }

                $form_state->set('fields_count', $max);
            } else {

                //unsetting the values of items if its removed form Form item
                foreach ($form_state->get('deletecounter') as $k => $v) {
                    unset($compdet['items'][$k]);
                }

                // Set item count 1 if all items removed form form
                if (empty($max) || is_null($max)) {
                    $max = array(0 => 0);
                    $form_state->set('fields_count', $max);
                }
            }


            foreach ($max as $delta => $value) {
                $dbaction = $form_state->getValue(['items', $delta, 'dbaction']);
                $dbact = isset($dbaction) ? $dbaction : (!empty($compdet['items'][$delta]['companyitempk']) ? 'u' : 'i');
                $compdet['items'][$delta]['dbaction'] = $dbact;
                $form['items'][$delta] = $this->contact_item_form($delta, $compdet['items'][$delta]);
            }
        } else {
            if (empty($max)) {
                $max = array(0 => 0);
                $form_state->set('fields_count', $max);
            }
            foreach ($max as $delta => $value) {
                $form['items'][$delta] = $this->contact_item_form($delta, array());
            }
        }
        ///  Item level Form ends

        $form['submitactionsdown'][] = $this->form_actions('down', $this->formmode);


        $form['#attached']['library'][] = 'mymodule/mymodule.lib';
        return $form;
    }

    public function form_actions($up_or_down = NULL, $mode) {
        if ($mode != 'DISPLAY') {
            $form['submit' . $up_or_down] = array(
                '#type' => 'submit',
                '#value' => t('Submit'),
            );
        }


        $link_options = array(
            'attributes' => array(
                'class' => array(
                    'btn',
                    'btn-danger',
                ),
            ),
        );
        $url = Url::fromRoute('mymodule_company_list');
        $url->setOptions($link_options);
//        $cancel_company = \Drupal::l(t('Cancel'), $url);
        $link_render_array = array(
            '#title' => array('#markup' => '<i class="fa fa-ban"></i> Cancel'),
            '#type' => 'link',
            '#url' => $url,
        );
        $cancel_company = \Drupal::service('renderer')->renderRoot($link_render_array);

        $form['cancel' . $up_or_down] = [
            '#markup' => $cancel_company,
        ];
        if ($mode == 'DISPLAY') {
$form['comments'] = \Drupal::formBuilder()->getForm('Drupal\commentapi\Form\CommentsModule');
}

        return $form;
    }

    public function compinfotab1($compdet) {

        if ($this->formmode == 'DISPLAY') {
            $readonly = 'readonly';
        }
        $form['company_name'] = [
            '#type' => 'textfield',
            '#title' => t('Company'),
            '#default_value' => $compdet['companyname'],
            '#attributes' => array(
                'class' => array($readonly), $readonly => $readonly
            ),
            '#prefix' => '<div class="row"><div class="col-md-6  col-sm-12">',
            '#suffix' => '</div>',
        ];

        $form['location'] = [
            '#type' => 'select',
            '#options' => array('BAN' => 'Bengaluru', 'CHN' => 'Chennai', 'MUM' => 'Mumbai'),
            '#title' => t('Location'),
            '#default_value' => $compdet['companyloc'],
            '#attributes' => array(
                'class' => array($readonly), $readonly => $readonly
            ),
            '#prefix' => '<div class="col-md-6 col-sm-10">',
            '#suffix' => '</div></div>',
        ];
        return $form;
    }

    public function compinfotab2($compdet) {
        if ($this->formmode == 'DISPLAY') {
            $readonly = 'readonly';
        }

        $form['company_ceo'] = [
            '#type' => 'textfield',
            '#title' => t('Company CEO'),
            '#default_value' => $compdet['company_ceo'],
            '#attributes' => array(
                'class' => array($readonly), $readonly => $readonly
            ),
            '#prefix' => '<div class="row"><div class="col-md-6  col-sm-12">',
            '#suffix' => '</div>',
        ];

        $form['employees'] = [
            '#type' => 'textfield',
            '#title' => t('Total Employees'),
            '#default_value' => $compdet['no_of_employees'],
            '#attributes' => array(
                'class' => array($readonly), $readonly => $readonly
            ),
            '#prefix' => '<div class="col-md-6 col-sm-10">',
            '#suffix' => '</div></div>',
        ];
        return $form;
    }

    public function contact_item_form($delta, $details) {

        if ($this->formmode == 'DISPLAY') {
            $readonly = 'readonly';
        }
//        $form['#tree'] = TRUE;
        $form['start'] = array(
            '#prefix' => '<div class="row">',
        );

        $form['name'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Name'),
            '#title_display' => 'invisible',
            '#attributes' => array(
                'class' => array($readonly), $readonly => $readonly
            ),
            '#default_value' => $details['empname'],
            '#prefix' => '<div class="col-md-5 col-sm-10">',
            '#suffix' => '</div>'
        );

        $form['phone'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Phone'),
            '#title_display' => 'invisible',
            '#attributes' => array(
                'class' => array($readonly), $readonly => $readonly
            ),
            '#default_value' => $details['phone'],
            '#prefix' => '<div class="col-md-5 col-sm-10">',
            '#suffix' => '</div>'
        );

        if ($this->formmode != 'DISPLAY') {
            $form['remove'] = [
                '#type' => 'submit',
                '#idx' => $delta,
                '#name' => "REMOVE" . $delta,
                '#removepk' => $details['companyitempk'],
                '#value' => t('-'),
                '#submit' => array('::removeCallback'),
                '#ajax' => [
                    'callback' => '::removefieldCallback',
                    'wrapper' => 'items-wrapper',
                ],
                '#limit_validation_errors' => TRUE,
                '#prefix' => '<div class="col-md-2 col-sm-10">',
                '#suffix' => '</div>'
            ];
        }
        $form['end'] = array(
            '#suffix' => '</div>',
        );
        $form['dbaction'] = array(
            '#type' => 'hidden',
            '#default_value' => isset($details['dbaction']) ? $details['dbaction'] : '',
        );
        $form['companyitempk'] = array(
            '#type' => 'hidden',
            '#default_value' => $details['companyitempk'],
        );
        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $vals = $form_state->getValues();

        switch ($this->formmode) {
            case 'NEW':
                $returnval = Mymodule_biz::save_company($form, $form_state);
                if ($returnval == 'FAIL') {
                    
                } else {
                    drupal_set_message(t("Company details Saved Successfully"));
                    $form_state->setRedirect('mymodule_display', array('companypk' => $returnval));
                }
                break;
            case 'EDIT':
                $returnval = Mymodule_biz::edit_company($form, $form_state, $this->companypk);
                if ($returnval == 'FAIL') {
                    
                } else {
                    drupal_set_message(t("Company Updated Successfully"));
                    $form_state->setRedirect('mymodule_display', array('companypk' => $returnval));
                }
                break;
            default:
                $form_state->setRedirect('companylist');
                break;
        }
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        $items = $form_state->getValues();

        foreach ($items['items'] as $k => $val) {
            if (empty($val['name'])) {
                $form_state->setErrorByName('items][' . $k . '][name', $this->t('Please Select the name @' . $k));
            }
        }
    }

    /**
     * Ajax submit to add new field.
     */
    public function addfieldsubmit(array &$form, FormStateInterface &$form_state) {
        $button_clicked = $form_state->getTriggeringElement();
        $idx = substr($button_clicked['#name'], 0, 5);

        if ($idx != 'REMOVE') {
            $max = $form_state->get('fields_count');
            $count = end($max);
            array_push($max, $count + 1);
            $form_state->set('fields_count', $max);
            $form_state->setValue(['items', $count + 1, 'dbaction'], 'i');
            $form_state->setRebuild(TRUE);
        } else {
            
        }
    }

    public function addfieldCallback(array &$form, FormStateInterface &$form_state) {
        return $form['items'];
    }

    /**
     * Ajax callback to remove a field.
     */
    public function removefieldCallback(array &$form, FormStateInterface &$form_state) {
        return $form['items'];
    }

    public function removeCallback(array &$form, FormStateInterface $form_state) {
        $button_clicked = $form_state->getTriggeringElement();
        $idx = substr($button_clicked['#name'], 6);
        $removepk = $button_clicked['#removepk'];
        $max = $form_state->get('fields_count');
        unset($max[$idx]);
        $form_state->set('fields_count', $max);

        if (!empty($removepk))
            $form_state->set(['deletecounter', $idx], $removepk);

        $form_state->setRebuild(TRUE);
    }

}
