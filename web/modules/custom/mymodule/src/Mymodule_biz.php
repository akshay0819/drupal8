<?php

namespace Drupal\mymodule;

Class Mymodule_biz {

    static function getcompanydet($companypk = NULL) {

        if (!empty($companypk)) {
            $headerquery = db_select('frtcompanies', 'comp');
            $headerquery->fields('comp');
            $headerquery->condition('companypk', $companypk, '=');
            $result = $headerquery->execute()->fetchAssoc();

            $itemquery = db_select('frtcompanydet', 'compdet');
            $itemquery->fields('compdet');
            $itemquery->condition('companypk', $companypk, '=');

            $itemresult = $itemquery->execute();

            while ($record = $itemresult->fetchAssoc()) {
                $result['items'][] = $record;
            }
        }

        return $result;
    }

    static function save_company($form, $form_state) {
        $values = $form_state->getValues();

        //DbTransaction
        $transaction = db_transaction();

        $insertid = db_insert('frtcompanies')
                ->fields(array(
                    'companyname' => $values['company_name'],
                    'companyloc' => $values['location'],
                    'company_ceo' => $values['company_ceo'],
                    'no_of_employees' => $values['employees']
                ))
                ->execute();

        if ($insertid) {

            foreach ($values['items'] as $k => $val) {
                $detpk = db_insert('frtcompanydet')
                        ->fields(array(
                            'companypk' => $insertid,
                            'empname' => $val['name'],
                            'phone' => $val['phone'],
                        ))
                        ->execute();
                if (!$detpk) {
                    $rb = 'YES';
                }
            }
        } else {
            $rb = 'YES';
        }


        if ($rb == 'YES') {
            $transaction->rollback();
        } else {
            return $insertid;
        }
    }

    static function edit_company($form, $form_state, $companypk = '') {
        $values = $form_state->getValues();
        //DbTransaction
        $transaction = db_transaction();

        $update = db_update('frtcompanies')
                ->fields(array(
                    'companyname' => $values['company_name'],
                    'companyloc' => $values['location'],
                    'company_ceo' => $values['company_ceo'],
                    'no_of_employees' => $values['employees'],
                ))
                ->condition('companypk', $companypk, '=')
                ->execute();

        if ($update) {

            foreach ($values['items'] as $k => $val) {
                if ($val['dbaction'] == 'u') {
                    $detpk = db_update('frtcompanydet')
                            ->fields(array(
                                'empname' => $val['name'],
                                'phone' => $val['phone'],
                            ))
                            ->condition('companyitempk', $val['companyitempk'])
                            ->execute();
                    if (!$detpk) {
                        $rb = 'YES';
                    }
                } else {
                    $detinspk = db_insert('frtcompanydet')
                            ->fields(array(
                                'companypk' => $companypk,
                                'empname' => $val['name'],
                                'phone' => $val['phone'],
                            ))
                            ->execute();
                    if (!$detinspk) {
                        $rb = 'YES';
                    }
                }
            }

            ///delete items which are in delete counter

            foreach ($form_state->get('deletecounter') as $key => $v) {
                db_delete('frtcompanydet')
                        ->condition('companyitempk', $v)
                        ->execute();
            }
        } else {
            $rb = 'YES';
        }


        if ($rb == 'YES') {
            $transaction->rollback();
        } else {
            return $companypk;
        }
    }

    static function delete_company($id) {
        db_delete('frtcompanies')
                ->condition('companypk', $id)
                ->execute();
    }

}
