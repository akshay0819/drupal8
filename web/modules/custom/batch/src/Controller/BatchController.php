<?php

namespace Drupal\batch\Controller;

use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\customutil\CustomUtils;

Class BatchController {

    public function batchlist() {

        $header = array(
//            'companypk' => t('Company'),
            'batchno' => t('Batch Number'),
            'batchdate' => t('Batch Date'),
            'productpk' => t('Product'),
            'operations' => t('Edit'),
            'deletecomp' => t('Delete'),
        );

        $rows = array();
        // $getlist=Product_Biz::getproductlist();
        $query = db_select('tragbatch', 'batch');
        $query->fields('batch');

        $getlist = $query->execute();


        $link_options = array('attributes' => array('class' => array('btn btn-xs default btn-editable'),),);
        $link_options_delete = array('attributes' => array('class' => array('btn', 'btn-danger'),),);
        foreach ($getlist as $item) {
            $idarray = array('batchpk' => $item->batchpk);
            $edit_batch = CustomUtils::editButton('batch_example_edit', $idarray, 'extrasmall', 'Edit');
            $delete_batch = CustomUtils::deleteButton('batch_example_delete', $idarray, 'extrasmall', 'Delete');
            $dispurl = Url::fromRoute('batch_example_display', array('batchpk' => $item->batchpk));
            $display_batch = \Drupal::l(t($item->batchno), $dispurl);

            // Row with attributes on the row and some of its cells.
            $rows[] = array(
                'data' => array($display_batch, $item->batchdate, $item->productpk, $edit_batch, $delete_batch)
            );
        }

        $form['tablebody'] = array(
            '#markup' => '',
            '#prefix' => '<div class="kt-portlet kt-portlet--mobile">',
            '#suffix' => '</div>'
        );

        $form['tableheadbody'] = array(
            '#markup' => '',
            '#prefix' => '<div class="kt-portlet__head kt-portlet__head--lg">',
            '#suffix' => '</div>'
        );
        $form['tablebody']['table_heading'] = [
            '#markup' => '<div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon">
                                    <i class="kt-font-brand flaticon2-line-chart"></i>
                                </span>
                                <h3 class="kt-portlet__head-title">
                                    Batch list
                                </h3>
                            </div>',
            '#prefix' => '<div class="kt-portlet__head kt-portlet__head--lg">',
            '#suffix' => '</div>'
        ];

//          $url = Url::fromUri('internal:/product/new',NULL);
//        $url = Url::fromRoute('mymodule_addcompany');
//        $url->setOptions($link_options);
//        $add_companylink = \Drupal::l(t('Add Company'), $url);
        $add_batch = CustomUtils::addButton('batch_example.form', '', 'medium', 'Add New Batch');

        $form['tablebody']['table_heading']['submit'] = [
            '#markup' => $add_batch,
            '#prefix' => '<div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="kt-portlet__head-actions">',
            '#suffix' => '</div></div></div>',
        ];




        $form['tablebody']['company_table'] = array(
            '#theme' => 'table',
            '#header' => $header,
            '#rows' => $rows,
            '#attributes' => array(
                'id' => 'kt_table_1',
                'class' => "table table-striped- table-bordered table-hover"
            ),
            '#prefix' => '<div class="kt-portlet__body">',
            '#suffix' => '</div>',
        );
//        $form['pager'] = array(
//            '#type' => 'pager'
//        );
  //      $form['#attached']['library'][] = 'productmaster/productmasterlib';


        return $form;
    }

}
