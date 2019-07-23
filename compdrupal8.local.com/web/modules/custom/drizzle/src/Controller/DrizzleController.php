<?php

namespace Drupal\drizzle\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\customutil\CustomUtils;

Class DrizzleController extends ControllerBase {

    public function drizzlelist() {

        $header = array(
//            'companypk' => t('Company'),
            'tuname' => t('Name'),
            'tstartdate' => t('Start Date'),
            'tenddate' => t('End Date'),
            'tstatus' => t('Status'),
            'edit' => t('Edit'),
            'deletecomp' => t('Delete'),
        );

        $rows = array();
        // $getlist=Product_Biz::getproductlist();
        $query = db_select('tfrttenant', 't');
        $query->leftJoin('tfrttenantuser', 'tu', 't.tpk = tu.tpk AND t.tname=tu.tuname');
        $query->fields('t');
        $query->fields('tu');
//        $tenantdet = $query->execute()->fetchAssoc();

        $getlist = $query->execute();


        foreach ($getlist as $item) {
            $idarray = array('tpk' => $item->tpk);
            $edit_drizzle = CustomUtils::editButton('drizzle.drizzleedit', $idarray, 'extrasmall', 'Edit');
            $delete_drizzle = CustomUtils::deleteButton('drizzle.drizzleedit', $idarray, 'extrasmall', 'Delete');
            $dispurl = Url::fromRoute('drizzle.drizzledisplay', array('tpk' => $item->tpk));
            $display_drizzle = \Drupal::l(t($item->tuname), $dispurl);

            // Row with attributes on the row and some of its cells.
            $rows[] = array(
                'data' => array($display_drizzle, $item->tstartdate, $item->tenddate, $item->tstatus, $edit_drizzle, $delete_drizzle)
            );
        }

        $form['tablebody'] = array(
            '#markup' => '',
            '#prefix' => '<div class="kt-portlet--mobile">',
            '#suffix' => '</div>'
        );

        $form['tableheadbody'] = array(
            '#markup' => '',
            '#prefix' => '<div class="kt-portlet__head kt-portlet__head--lg">',
            '#suffix' => '</div>'
        );
        $form['tablebody']['table_heading'] = [
            '#markup' => '<div class="kt-portlet__head-label">' .
//                                <span class="kt-portlet__head-icon">
//                                    <i class="kt-font-brand flaticon2-line-chart"></i>
//                                </span>
//                                <h3 class="kt-portlet__head-title">
//                                    Batch list
//                                </h3>
            '</div>',
            '#prefix' => '<div class="kt-portlet__head kt-portlet__head--lg">',
            '#suffix' => '</div>'
        ];

        $add_batch = CustomUtils::addButton('drizzle.drizzleadd', '', 'medium', 'Add New User');

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
//            '#prefix' => '<div class="kt-portlet__body">',
//            '#suffix' => '</div>',
        );
//        $form['pager'] = array(
//            '#type' => 'pager'
//        );
        $form['#attached']['library'][] = 'productmaster/productmasterlib';


        return $form;
    }

    static function drizzle_load_from_db($tpk) {

        $query = db_select('tfrttenant', 'tnt');
        $query->fields('tnt');
        $query->condition('tpk', $tpk, '=');

        $drizzle = $query->execute()->fetchAssoc();

        $query2 = db_select('tfrttenantuser', 'tu');
        $query2->fields('tu');
        $query2->condition('tpk', $tpk, '=');
        $query2->condition('tuname', $tpk, '!=');

        $drizzledetail = $query2->execute();
   
        $item = array();
        while ($record = $drizzledetail->fetchAssoc()) {

            foreach ($record as $k => $val) {
                $item[$k] = $val;
                $item['dbaction'] = 'u';
            }
            $items[] = $item;
        }

        $fritems = $drizzle;


        $fritems['items'] = $items;

        return $fritems;
    }

}
