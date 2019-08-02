<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\signup;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;

/**
 * Description of SignupActivation
 *
 * @author anu
 */
class SignupActivation extends FormBase {

    public function getFormId() {
        return 'user_signup_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state, $activatelink = '') {
        $this->activatelink = $activatelink;
        $config = \Drupal::config('drizzle.settings');
        $database=$config->get('dbconnection.database');

        $txn = \Drupal::database()->startTransaction();

        $itemquery = db_select('tfrtsignup', 'tus');
        $itemquery->fields('tus');
        $itemquery->condition('mailurl', $this->activatelink, '=');
        $itemquery->range(0, 1);

        $itemresult = $itemquery->execute()->fetchAssoc();

        //get user details to check whther user is activated or not
        $useruery = db_select('users_field_data', 'ufd');
        $useruery->fields('ufd', array('status'));
        $useruery->condition('name', $itemresult['firstname'], '=');
        $useruery->condition('mail', $itemresult['mailid'], '=');
        $useruery->range(0, 1);

        $usersresult = $useruery->execute()->fetchAssoc();


        if ($usersresult['status'] == 1) {
            $this->messenger()->addMessage(t('Your Account is already activated'), 'error');
        } else {

            db_transaction();
            $timstamp = time();
            $mysqluser = strtolower(substr(str_replace(' ', '', $itemresult['company']), 0, 5)) . $timstamp;
            $pre_pass = md5($mysqluser);
            $p_arr = str_split($pre_pass, 8);
            $pass = $p_arr['3'] . $p_arr['1'] . $p_arr['2'] . $p_arr['0'];
            $enddate = date('Y-m-d', strtotime("+30 days"));

            $createuser = \Drupal::database()->query("Create user " . $mysqluser . "  identified by '" . $pass . "'");
            if($createuser){
                  $grantperm = \Drupal::database()->query( "Grant select, insert, update, delete, execute, trigger on " . $database . ".* to '" . $mysqluser . "'");
                  if($grantperm){

                            $tenantpk = db_insert('tfrttenant')
                                ->fields(array(
                                    'tenant' =>  $mysqluser,
                                    'tname' => $itemresult['mailid'],
                                    'company' => $itemresult['company'],
                                    'tstatus' => 'active',
                                    'tstartdate' => date('Y-m-d'),
                                    'tenddate' => $enddate
                                  ))
                                ->execute();
                                if($tenantpk){
                                    $tenantuserpk = db_insert('tfrttenantuser')
                                           ->fields(array(
                                    'tpk' =>  $tenantpk,
                                    'tuname' => $itemresult['mailid'],
                                    'tufname' => $itemresult['firstname'],
                                    'tulname' => $itemresult['firstname'],
                                    'tustatus' => 'active'
                                  ))
                                ->execute();
                                    if($tenantuserpk){

                                } else {
                                    $rb = 'YES';
                                }

                                } else {
                                    $rb = 'YES';
                                }


                  }else{
                    $rb = 'YES';
                  }


                    $updatequery = db_update('users_field_data')
                    ->fields(array('status' => 1))
                    ->condition('name', $itemresult['firstname'], '=')
                    ->condition('mail', $itemresult['mailid'], '=')
                    ->execute();

                        if (!$updatequery) {
                            $rb = 'YES';
                        } else {
                            $updatesignup = db_update('tfrtsignup')
                                ->fields(array(
                                    'status' => 'active',
                                    'tenant' =>  $mysqluser,
                                ))
                                ->condition('mailid', $itemresult['mailid'], '=')
                                ->execute();
                                if (!$updatesignup) {
                                   $rb = 'YES';
                                 }
                        }

            } else {



        }

             if($rb=='YES'){
                 $txn->rollBack();
                 $this->messenger()->addMessage(t('Activation is Un-Successfull.'));
             }else{
                 $this->messenger()->addMessage(t('Activation is Successfull.'));
             }

           
        }


        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        
    }

}
