<?php

namespace Drupal\formmodule\Controller;

Class Formmodule_biz {

    static function getformmoduledet($apmdgpk = NULL, $appformpk = NULL) {

        if (!empty($apmdgpk)) {
            $headerquery = db_select('appform', 'a');
            $headerquery->fields('a');
            $headerquery->condition('appformid', $apmdgpk, '=');
            $headerquery->condition('appformpk', $appformpk, '=');
            $result = $headerquery->execute()->fetchAssoc();

        }

        return $result;
    }

    static function save_formmodule($form, $form_state, $apmdgpk = '') {
        $values = $form_state->getValues();
        //DbTransaction
        $transaction = db_transaction();
	$insertid = db_insert('appform')
                ->fields(array(
                    'appformid' => $apmdgpk,
                    'appgroupfields' => json_encode($values, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)
                ))
                ->execute();

        if (!$insertid) {
            $transaction->rollback();
        } else {
            return $insertid;
        }
   }



    static function edit_formmodule($form, $form_state, $apmdgpk = '', $appformpk = '') {

        $values = $form_state->getValues();
        //DbTransaction

        $transaction = db_transaction();

        $update = db_update('appform')
                ->fields(array(
                    'appgroupfields' => json_encode($values, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)
                ))
                ->condition('appformid', $apmdgpk, '=')
                ->condition('appformpk', $appformpk, '=')
                ->execute();

        if (!$update) {
            $transaction->rollback();
        } else {
            return $update;
        }
    }

    static function delete_formmodule($appformpk) {
        db_delete('appform')
                ->condition('appformpk', $appformpk)
                ->execute();
    }

}
