<?php

namespace Drupal\productmaster;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\productmaster\Product_utils;
use Drupal\productmaster\Product_Biz;
use Drupal\Core\Datetime\DrupalDateTime;

Class AddProduct extends FormBase {

    public $id;

    public function buildForm(array $form, FormStateInterface $form_state, $formmode = '', $id = '') {
        $this->id = $id;
        $this->formmode = $formmode;
        $display_mode = FALSE;
        if ($this->formmode == 'DISPLAY') {

            $this->display_mode = TRUE;
        }

        $proddet = Product_Biz::getproductdet($id);
        $details = Product_Biz::getproductsdetail($id);

//        $form['productinformation'] = [
//            '#type' => 'vertical_tabs',
//            '#default_tab' => 'edit-prodinfo',
//        ];
//        // 1St tab
//        $form['prodinfo'] = [
//            '#type' => 'details',
//            '#title' => 'Product Information',
//            '#group' => 'productinformation',
//        ];
//        // 2nd tab
//        $form['prodimg'] = [
//            '#type' => 'details',
//            '#title' => 'Product Image',
//            '#group' => 'productinformation',
//        ];
//        // 3rd tab
//        $form['organic'] = [
//            '#type' => 'details',
//            '#title' => 'Organic',
//            '#group' => 'productinformation',
//        ];
        $form['formcoverstart']['#markup'] = '<div class="kt-portlet">';
        $form['formbody']['#markup'] = '<div class="kt-portlet__body">';
        
        $form['tabs'] = [
            '#markup' => '',
            '#prefix' => '<ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand" role="tablist">',
            '#suffix' => '</ul>',
        ];
        $form['tabs']['one'] = [
            '#markup' => '<a class="nav-link active" data-toggle="tab" href="#kt_tabs_2_1">Product info tab1</a>',
            '#prefix' => '<li class="nav-item">',
            '#suffix' => '</li>',
        ];
        $form['tabs']['two'] = [
            '#markup' => '<a class="nav-link" data-toggle="tab" href="#kt_tabs_2_2">Product info tab2</a>',
            '#prefix' => '<li class="nav-item">',
            '#suffix' => '</li>',
        ];
        $form['tabs']['three'] = [
            '#markup' => '<a class="nav-link" data-toggle="tab" href="#kt_tabs_2_3">Product info tab3</a>',
            '#prefix' => '<li class="nav-item">',
            '#suffix' => '</li>',
        ];

        $form['tabscontent'] = [
//            '#markup' => '',
            '#prefix' => '<div class="tab-content">',
            '#suffix' => '</div>',
        ];
        $form['tabscontent']['one'] = [
//            '#markup' =>  '',
            '#prefix' => '<div class="tab-pane active" id="kt_tabs_2_1" role="tabpanel">',
            '#suffix' => '</div>',
        ];
        $form['tabscontent']['two'] = [
//            '#markup' =>  '',
            '#prefix' => '<div class="tab-pane" id="kt_tabs_2_2" role="tabpanel">',
            '#suffix' => '</div>',
        ];
        $form['tabscontent']['three'] = [
            '#markup' => '',
            '#prefix' => '<div class="tab-pane" id="kt_tabs_2_3" role="tabpanel">',
            '#suffix' => '</div>',
        ];

        $form['tabscontent']['one']['productinfo1'] = $this->productinfotab1($proddet);
        $form['tabscontent']['two']['productinfo2'] = $this->productinfotab2($proddet);
        $form['tabscontent']['three']['productinfo3'] = $this->productinfotab3($proddet);
        $form['formbodyend']['#markup'] = '</div>';
        $form['formcoverend']['#markup'] = '</div>';
        // Do not flatten nested form fields
        // $form['#tree'] = TRUE;



        $form['field_container'] = array(
            '#type' => 'container',
//            '#weight' => 80,
            '#tree' => TRUE,
            // Set up the wrapper so that AJAX will be able to replace the fieldset.
            '#prefix' => '<div id="js-ajax-elements-wrapper">',
            '#suffix' => '</div>',
        );
        //  echo '<pre/>';
        $count = count($details); //exit;
//drupal_set_message($form_state->get('field_deltas'));
        if ($form_state->get('field_deltas') == '') {
            $form_state->set('field_deltas', range(0, $count - 1));
        }

        $field_count = $form_state->get('field_deltas');
        // if ($form_state->get('arraycount') == '') {
        $form_state->set('arraycount', $count);
//        }


        $form['field_container']['add_name'] = array(
            '#type' => 'submit',
            '#value' => t('Add one more'),
            '#submit' => array('::mymoduleAjaxExampleAddMoreAddOne'),
            '#ajax' => array(
                'callback' => '::mymoduleAjaxExampleAddMoreAddOneCallback',
                'wrapper' => 'js-ajax-elements-wrapper',
            ),
            '#prefix' => '<div class="row"><br/>',
            '#suffix' => '</div>',
        );

        foreach ($field_count as $delta) {
            $form['field_container'][$delta] = array(
                '#prefix' => '<div class="row">',
                '#suffix' => '</div>',
                '#tree' => TRUE,
            );

            $form['field_container'][$delta]['fieldpk'] = array(
                '#type' => 'hidden',
                '#default_value' => $details[$delta]->proddetpk,
            );

            $form['field_container'][$delta]['field1'] = array(
                '#type' => 'textfield',
                '#title' => t('Field 1 - ' . $delta),
                '#default_value' => $details[$delta]->field1,
                '#prefix' => '<div class="col-md-5 col-sm-10">',
                '#suffix' => '</div>',
            );

            $form['field_container'][$delta]['field2'] = array(
                '#type' => 'textfield',
                '#title' => t('Field 2 - ' . $delta),
                '#default_value' => $details[$delta]->field2,
                '#prefix' => '<div class="col-md-5 col-sm-10">',
                '#suffix' => '</div>',
            );

            $form['field_container'][$delta]['remove_name'] = array(
                '#type' => 'submit',
                '#value' => t('-'),
                '#submit' => array('::mymoduleAjaxExampleAddMoreRemove'),
                '#ajax' => array(
                    'callback' => '::mymoduleAjaxExampleAddMoreRemoveCallback',
                    'wrapper' => 'js-ajax-elements-wrapper',
                ),
                '#attributes' => array(
                    'class' => array('button-small'),
                ),
                '#name' => 'remove_name_' . $delta,
                '#prefix' => '<div class="col-md-2 col-sm-10">',
                '#suffix' => '</div>',
            );
        }




        if (!isset($this->display_mode)) {

            $form['actions']['submit'] = [
                '#type' => 'submit',
                '#value' => $this->t('Submit'),
                '#attributes' => array('class' => array('btn'))
                    //'#description' => $this->t('Submit, #type = submit'),
            ];

            $link_options = array(
                'attributes' => array(
                    'class' => array(
                        'btn',
                        'btn-primary',
                    ),
                ),
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
        $url = Url::fromRoute('productmaster_list');
        $url->setOptions($link_options);
        $cancel_productlink = \Drupal::l(t('Cancel'), $url);

        $form['actions']['cancel'] = [
            '#markup' => $cancel_productlink,
        ];
        $form['#attributes']['enctype'] = 'multipart/form-data';
        $form['#attached']['library'][] = 'productmaster/productmaster_lib';
        return $form;
    }

    public function productinfotab1($proddet) {

        $form['productcode'] = [
            '#type' => 'textfield',
            '#title' => t('Product Code'),
            '#default_value' => $proddet['productcode'],
            '#prefix' => '<div class="row"><div class="col-md-4  col-sm-12">',
            '#suffix' => '</div>',
        ];

        $form['pglclass'] = [
            '#type' => 'select',
            '#options' => array('ragi' => 'Ragi', 'rice' => 'Rice', 'wheat' => 'Wheat'),
            '#title' => t('G/L Classification'),
            '#default_value' => $proddet['pglclass'],
            '#prefix' => '<div class="col-md-4 col-sm-10">',
            '#suffix' => '</div>',
        ];

        $form['productdesc'] = [
            '#type' => 'textfield',
            '#default_value' => $proddet['productdesc'],
            '#title' => t('Product Desc'),
            '#prefix' => '<div class="col-md-4 col-sm-10">',
            '#suffix' => '</div>',
        ];
        $prodtypes = Product_utils::productmaster_get_producttypes('PCATG');

        $form['productcatg'] = [
            '#type' => 'select',
            '#options' => $prodtypes,
            '#default_value' => $proddet['productcatg'],
            '#title' => $this->t('Product Category'),
            '#prefix' => '<div class="col-md-4 col-sm-12">',
            '#suffix' => '</div>',
        ];
        $form['producttype'] = [
            '#type' => 'select',
            '#options' => $prodtypes,
            '#default_value' => $proddet['producttype'],
            '#title' => t('Product Types'),
            '#prefix' => '<div class="col-md-4 col-sm-10">',
            '#suffix' => '</div>',
        ];


        $form['productrating'] = [
            '#type' => 'select',
            '#options' => array('high' => 'High', 'medium' => 'Medium', 'low' => 'Low'),
            '#default_value' => $proddet['productrating'],
            '#title' => $this->t('Product Rating'),
            '#prefix' => '<div class="col-md-4 col-sm-12">',
            '#suffix' => '</div></div>',
        ];

        return $form;
    }

    public function productinfotab2($proddet) {

        $form['productimg'] = [
            '#type' => 'file',
            '#title' => t('Product Image'),
            '#prefix' => '<div class="row"><div class="col-md-12 col-sm-12">',
            '#suffix' => '</div></div>',
        ];
        return $form;
    }

    public function productinfotab3($proddet) {
        $prodtypes = Product_utils::productmaster_get_producttypes('ACCT');

        $form['orgstatus'] = [
            '#type' => 'textfield',
            '#title' => t('Organic Status'),
            '#default_value' => $proddet['orgstatus'],
            '#prefix' => '<div class="row"><div class="col-md-6 col-sm-12">',
            '#suffix' => '</div>',
        ];

        $form['croptype'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Crop Type'),
            '#default_value' => $proddet['croptype'],
            '#prefix' => '<div class="col-md-6 col-sm-12">',
            '#suffix' => '</div>',
        ];

        $form['season'] = [
            '#type' => 'textfield',
            '#default_value' => $proddet['season'],
            '#title' => $this->t('Season Of Crop'),
            '#prefix' => '<div class="col-md-6 col-sm-12">',
            '#suffix' => '</div>',
        ];


        $form['date'] = [
            '#title' => $this->t('Date'),
            '#type' => 'date',
            '#attributes' => array('type' => 'date', 'min' => '-25 years', 'max' => '+5 years'),
            '#date_format' => 'd/m/Y',
            '#prefix' => '<div class="col-md-6 col-sm-12">',
            '#suffix' => '</div></div>',
        ];


        return $form;
    }

    /**
     * @param array $form
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     */
    function mymoduleAjaxExampleAddMoreRemove(array &$form, FormStateInterface $form_state) {
        // Get the triggering item
        $delta_remove = $form_state->getTriggeringElement()['#parents'][1];

        // Store our form state
        $field_deltas_array = $form_state->get('field_deltas');

        // Find the key of the item we need to remove
        $key_to_remove = array_search($delta_remove, $field_deltas_array);

        // Remove our triggered element
        unset($field_deltas_array[$key_to_remove]);

        // Rebuild the field deltas values
        $form_state->set('field_deltas', $field_deltas_array);

        // Rebuild the form
        $form_state->setRebuild();

        // Return any messages set
        drupal_get_messages();
    }

    /**
     * @param array $form
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *
     * @return mixed
     */
    function mymoduleAjaxExampleAddMoreRemoveCallback(array &$form, FormStateInterface $form_state) {
        return $form['field_container'];
    }

    /**
     * @param array $form
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     */
    function mymoduleAjaxExampleAddMoreAddOne(array &$form, FormStateInterface $form_state) {

        // Store our form state
        $field_deltas_array = $form_state->get('field_deltas');
        $fieldstotal = $form_state->get('arraycount');
        if (empty($fieldstotal)) {
            $fieldstotal = 0;
        };
        // check to see if there is more than one item in our array
        if (count($field_deltas_array) > 0) {
            // Add a new element to our array and set it to our highest value plus one
            $field_deltas_array[] = max($field_deltas_array) + 1;
        } else {
            // Set the new array element to 0
            $field_deltas_array[] = 0;
        }

        // Rebuild the field deltas values
        $form_state->set('field_deltas', $field_deltas_array);

        $form_state->setRebuild();

        // Return any messages set
        drupal_get_messages();
    }

    /**
     * @param array $form
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *
     * @return mixed
     */
    function mymoduleAjaxExampleAddMoreAddOneCallback(array &$form, FormStateInterface $form_state) {
        return $form['field_container'];
    }

    public function getFormId() {
        return 'main_product_form';
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {


//        if (empty($_FILES['files']['name'])) {
//            $form_state->setErrorByName('productimg', $this->t('Please Select the Product Image'));
//        }
//        if (empty($form_state->getValue('productdesc'))) {
//            $form_state->setErrorByName('productdesc', $this->t('Please Enter the Product Description'));
//        }
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {


        switch ($this->formmode) {
            case 'NEW':
                $returnval = Product_Biz::save_product($form, $form_state);
                if ($returnval == 'FAIL') {
                    
                } else {
                    drupal_set_message(t("Product Saved Successfully"));
                    $form_state->setRedirect('productmaster_display', array('id' => $returnval));
                }
                break;
            case 'EDIT':
                $returnval = Product_Biz::edit_product($form, $form_state, $this->id);
                if ($returnval == 'FAIL') {
                    
                } else {
                    drupal_set_message(t("Product Updated Successfully"));
                    $form_state->setRedirect('productmaster_display', array('id' => $returnval));
                }
                break;
            default:
                $form_state->setRedirect('productmaster_list');
                break;
        }
    }

}
