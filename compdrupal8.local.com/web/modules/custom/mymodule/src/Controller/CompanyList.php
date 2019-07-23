<?php

namespace Drupal\mymodule\Controller;

use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\customutil\CustomUtils;

/**
 * Description of CompanyList
 *
 * @author anu
 */
Class CompanyList {

    public function companieslist() {

              $header = array(
//            'companypk' => t('Company'),
            'companyname' => t('Company Name'),
            'companyloc' => t('Location'),
            'operations' => t('Edit'),
            'deletecomp' => t('Delete'),
        );

        $rows = array();
        // $getlist=Product_Biz::getproductlist();
        $query = db_select('frtcompanies', 'comp');
        $query->fields('comp');
//        $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')->orderByHeader($header);
//        $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(10);
        $getlist = $query->execute();


        $link_options = array('attributes' => array('class' => array('btn btn-xs default btn-editable'),),);
        $link_options_delete = array('attributes' => array('class' => array('btn', 'btn-danger'),),);
        foreach ($getlist as $item) {
            $idarray = array('companypk' => $item->companypk);
            $edit_company = CustomUtils::editButton('mymodule_editcompany', $idarray, 'extrasmall', 'Edit');
            $delete_company = CustomUtils::deleteButton('mymodule_delete', $idarray, 'extrasmall', 'Delete');
            $dispurl = Url::fromRoute('mymodule_display', array('companypk' => $item->companypk));
            $display_company = \Drupal::l(t($item->companyname), $dispurl);

            // Row with attributes on the row and some of its cells.
            $rows[] = array(
                'data' => array($display_company, $item->companyloc, $edit_company, $delete_company)
            );
        }

$form['tablebody']= array(
'#markup'=>'',
'#prefix' => '<div class="kt-portlet kt-portlet--mobile">',
'#suffix' => '</div>'
);

$form['tableheadbody']= array(
'#markup'=>'',
'#prefix' => '<div class="kt-portlet__head kt-portlet__head--lg">',
'#suffix' => '</div>'
);
        $form['tablebody']['table_heading'] = [
            '#markup' => '<div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon">
                                    <i class="kt-font-brand flaticon2-line-chart"></i>
                                </span>
                                <h3 class="kt-portlet__head-title">
                                   CompanyList
                                </h3>
                            </div>',     
  '#prefix' => '<div class="kt-portlet__head kt-portlet__head--lg">',
'#suffix' => '</div>'                               

        ];

//          $url = Url::fromUri('internal:/product/new',NULL);
//        $url = Url::fromRoute('mymodule_addcompany');
//        $url->setOptions($link_options);
//        $add_companylink = \Drupal::l(t('Add Company'), $url);
        $add_companylink = CustomUtils::addButton('mymodule_addcompany', '', 'medium', 'Add Company');

       $form['tablebody']['table_heading']['submit'] = [
            '#markup' => $add_companylink,
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
         $form['#attached']['library'][] = 'productmaster/productmaster.lib';


        return $form;
    }

}
