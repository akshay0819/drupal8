<?php

namespace Drupal\formbuild\Controller;

Class Formbuild_biz {

    static function getformbuilddet($formbuildpk = NULL) {
	$result = array();
        if (!empty($formbuildpk)) {
            $headerquery = db_select('appmdgroup', 'a');
            $headerquery->fields('a');
            $headerquery->condition('apmdgpk', $formbuildpk, '=');
            $result = $headerquery->execute()->fetchAssoc();

        }

        return $result;
    }

    static function save_formbuild($form, $form_state) {
        $values = $form_state->getValues();
        //DbTransaction
        $transaction = db_transaction();

        $insertid = db_insert('appmdgroup')
                ->fields(array(
                    'apmdgroupid' => $values['apmdgroupid'],
                    'apmdgroupname' => $values['apmdgroupname'],
                    'apmdfields' => json_encode(explode(",", str_replace(" ", "", $values['apmdfields']))),
                    'aptablefields' => json_encode(explode(",", str_replace(" ", "", $values['aptablefields']))),
                    'apkeyfields' => json_encode(explode(",", str_replace(" ", "", $values['apkeyfields']))),
		    'createdby' => \Drupal::currentUser()->id()
                ))
                ->execute();

        if (!$insertid) {
            $transaction->rollback();
        } else {
            return $insertid;
        }
   }



    static function edit_formbuild($form, $form_state, $apmdgpk = '') {

        $values = $form_state->getValues();
        //DbTransaction

        $transaction = db_transaction();

        $update = db_update('appmdgroup')
                ->fields(array(
                    'apmdgroupid' => $values['apmdgroupid'],
                    'apmdgroupname' => $values['apmdgroupname'],
                    'apmdfields' => json_encode(explode(",", str_replace(" ", "", $values['apmdfields']))),
                    'aptablefields' => json_encode(explode(",", str_replace(" ", "", $values['aptablefields']))),
                    'apkeyfields' => json_encode(explode(",", str_replace(" ", "", $values['apkeyfields']))),
		    'updatedby' => \Drupal::currentUser()->id(),
                    'updatedtime' => date('Y-m-d H:i:s', time())
                ))
                ->condition('apmdgpk', $apmdgpk, '=')
                ->execute();

        if (!$update) {
            $transaction->rollback();
        } else {
            return $update;
        }
    }

    static function delete_formbuild($apmdgpk) {
        db_delete('appmdgroup')
                ->condition('apmdgpk', $apmdgpk)
                ->execute();
    }

}
