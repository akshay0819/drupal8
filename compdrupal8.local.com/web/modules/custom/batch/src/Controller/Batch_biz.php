<?php

namespace Drupal\batch\Controller;

Class Batch_biz {

    static function getbatchdet($batchpk = NULL) {

        if (!empty($batchpk)) {
            $headerquery = db_select('xtragbatch', 'batch');
            $headerquery->fields('batch');
            $headerquery->condition('batchpk', $batchpk, '=');
            $result = $headerquery->execute()->fetchAssoc();

            $itemquery = db_select('trproductinfo', 'batchdet');
            $itemquery->fields('batchdet');
            $itemquery->condition('batchpk', $batchpk, '=');

            $itemresult = $itemquery->execute();

            while ($record = $itemresult->fetchAssoc()) {
                 $result[$record['lablecode']] = $record;
            }
        }

        return $result;
    }

    static function save_batch($form, $form_state) {
        $values = $form_state->getValues();
        $customtemplatepk = $values['customtemplatepk'];
        //DbTransaction
        $transaction = db_transaction();

        $insertid = db_insert('xtragbatch')
                ->fields(array(
                    'batchno' => $values['batchno'],
                    'batchdate' => $values['batchdate'],
                    'packdate' => $values['packdate'],
                    'usebydate' => $values['usebydate'],
                    'netweight' => $values['netweight'],
                    'packedweight' => $values['packedweight'],
                    'batchtext' => $values['batchtext'],
                    'productdesc' => $values['productcode'],
                    'customtemplatepk' => $customtemplatepk,
                ))
                ->execute();

        if ($insertid) {


            $query = db_select('tragcustomtemplatefields', 'tempfields');
            $query->fields('tempfields', ['fcode', 'source']);
            $query->condition('customtemplatepk', $customtemplatepk, '=');
            $result = $query->execute()->fetchAll();
            foreach ($result as $k => $val) {
                $fcode = $values[$val->fcode];
                $insertprod = db_insert('trproductinfo')
                        ->fields(array(
                            'batchpk' => $insertid,
                            'customtemplatepk' => $customtemplatepk,
                            'productpk' => 1, //$values['productcode'],
                            'lablecode' => $val->fcode,
                            'labledesc' => $fcode,
                                // 'updatedby' => $values['batchtext'],
                        ))
                        ->execute();
                if (!$insertprod) {
                    $rb = 'YES';
                }
            }
        


        if ($rb == 'YES') {
            $transaction->rollback();
        } else {
            return $insertid;
        }
      }
   }

    static function edit_batch($form, $form_state, $batchpk = '') {
        $values = $form_state->getValues();
        //DbTransaction

        $transaction = db_transaction();

        $update = db_update('xtragbatch')
                ->fields(array(
                    'batchno' => $values['batchno'],
                    'batchdate' => $values['batchdate'],
                    'packdate' => $values['packdate'],
                    'usebydate' => $values['usebydate'],
                    'netweight' => $values['netweight'],
                    'packedweight' => $values['packedweight'],
                    'batchtext' => $values['batchtext'],
                    'productdesc' => $values['productcode'],
                    'customtemplatepk' => $customtemplatepk,
                ))
                ->condition('batchpk', $batchpk, '=')
                ->execute();

        if ($update) {


        } else {
            $rb = 'YES';
        }


        if ($rb == 'YES') {
            $transaction->rollback();
        } else {
            return $update;
        }
    }

    static function delete_batch($batchpk) {
        db_delete('tragbatch')
                ->condition('batchpk', $batchpk)
                ->execute();
    }

}
