<?php

namespace Drupal\formfields\Controller;

Class Formfields_biz {

    static function getformfieldsdet($formbuildpk = NULL) {
	$result = array();
        if (!empty($formbuildpk)) {
            $headerquery = db_select('appmetadata', 'a');
            $headerquery->fields('a');
            $headerquery->condition('apmdpk', $formbuildpk, '=');
            $result = $headerquery->execute()->fetchAssoc();

        }

        return $result;
    }

    static function save_formfields($form, $form_state) {
        $values = $form_state->getValues();
        //DbTransaction
        $transaction = db_transaction();

        $insertid = db_insert('appmetadata')
                ->fields(array(
                    'apmdtype' => $values['apmdtype'],
                    'apmdlength' => $values['apmdlength'],
                    'apmddesc' => $values['apmddesc'],
                    'apmdname' => $values['apmdname'],
                    'apmdoptions' => $values['apmdoptions'],
                    'createdby' => \Drupal::currentUser()->id()
                ))
                ->execute();

        if (!$insertid) {
            $transaction->rollback();
        } else {
            return $insertid;
        }
   }



    static function edit_formfields($form, $form_state, $apmdpk = '') {

        $values = $form_state->getValues();
        //DbTransaction

        $transaction = db_transaction();

        $update = db_update('appmetadata')
                ->fields(array(
                    'apmdtype' => $values['apmdtype'],
                    'apmdlength' => $values['apmdlength'],
                    'apmddesc' => $values['apmddesc'],
                    'apmdname' => $values['apmdname'],
                    'apmdoptions' => $values['apmdoptions'],
                    'updatedby' => \Drupal::currentUser()->id(),
                    'updatedtime' => date('Y-m-d H:i:s', time())
                ))
                ->condition('apmdpk', $apmdpk, '=')
                ->execute();

        if (!$update) {
            $transaction->rollback();
        } else {
            return $update;
        }
    }

    static function delete_formfields($apmdpk) {
        db_delete('appmetadata')
                ->condition('apmdpk', $apmdpk)
                ->execute();
    }

}
