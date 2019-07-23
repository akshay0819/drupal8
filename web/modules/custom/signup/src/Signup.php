<?php

namespace Drupal\signup;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Test\AssertMailTrait;

Class Signup extends FormBase {

    protected $mailManager;
    protected $languageManager;

    public function __construct(MailManagerInterface $mail_manager, LanguageManagerInterface $language_manager) {
        $this->mailManager = $mail_manager;
        $this->languageManager = $language_manager;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        $form = new static(
                $container->get('plugin.manager.mail'), $container->get('language_manager')
        );
        $form->setMessenger($container->get('messenger'));
        return $form;
    }

    public function getFormId() {
        return 'user_signup_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {

        $config = \Drupal::config('drizzle.settings');
// Will print 'Hello'.
// print $config->get('text_val');
        $form['main'] = [
            '#type' => 'fieldset',
            '#title' => t('User Details'),
            '#group' => 'productinformation',
            '#prefix' => '<div id="container">',
            '#suffix' => '</div>',
        ];
        $form['main']['row'] = [
            '#markup' => '',
            '#prefix' => '<div class="row">',
            '#suffix' => '</div>',
        ];

        $form['main']['row']['name'] = [
            '#type' => 'textfield',
            '#title' => t('First Name'),
            '#prefix' => '<div class="col-md-6">',
            '#suffix' => '</div>',
        ];

        $form['main']['row']['lastname'] = [
            '#type' => 'textfield',
            '#title' => t('Last Name'),
            '#prefix' => '<div class="col-md-6  col-sm-12">',
            '#suffix' => '</div>',
        ];        

        $form['main']['row']['email'] = [
            '#type' => 'email',
            '#title' => t('Email'),
            '#prefix' => '<div class="col-md-6  col-sm-12">',
            '#suffix' => '</div>',
        ];

        $form['main']['row']['mobile'] = [
            '#type' => 'textfield',
            '#title' => t('Mobile No.'),
            '#prefix' => '<div class="col-md-6  col-sm-12">',
            '#suffix' => '</div>',
        ];

        $form['main']['row']['username'] = [
            '#type' => 'textfield',
            '#title' => t('Username'),
            '#prefix' => '<div class="col-md-6  col-sm-12">',
            '#suffix' => '</div>',
        ];
        
       $form['main']['row']['company'] = [
           '#type' => 'textfield',
           '#title' => t('Company'),
           '#prefix' => '<div class="col-md-6 col-sm-12">',
           '#suffix' => '</div>',
       ];        


        $form['main']['row']['userpassword'] = [
            '#type' => 'password',
            '#title' => t('Password'),
            '#prefix' => '<div class="col-md-6  col-sm-12">',
            '#suffix' => '</div>',
        ];

        $form['main']['row']['confirmpassword'] = [
            '#type' => 'password',
            '#title' => t('Confirm password'),
            '#prefix' => '<div class="col-md-6 col-sm-12">',
            '#suffix' => '</div>',
        ];


        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => t('Signup'),
        );

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        //parent::validateForm($form, $form_state);
        $values = $form_state->getValues();

        if (empty($values['name'])) {
            $form_state->setErrorByName('name', t('Please Enter Your Name.'));
        }
        if (empty($values['email'])) {
            $form_state->setErrorByName('email', t('Please Enter Your email.'));
        }
        if (empty($values['username'])) {
            $form_state->setErrorByName('username', t('Please Enter User Name'));
        }
        if (empty($values['company'])) {
            $form_state->setErrorByName('company', t('Please Enter Company'));
        }
        if (empty($values['userpassword'])) {
            $form_state->setErrorByName('userpassword', t('Please Enter password'));
        }
        if ($values['userpassword'] != $values['confirmpassword']) {
            $form_state->setErrorByName('userpassword', t('Passwords are not matching'));
            $form_state->setErrorByName('confirmpassword', t(' '));
        }


$mailid = db_select('tfrtsignup', 'tsnp')
                ->fields('tsnp', array('mailid'))
                ->condition('mailid', $values['email'], '=')
                ->execute()
                ->fetchField();
$mailid2 = db_select('tfrttenantuser', 'tuser')
                ->fields('tuser', array('tuname'))
                ->condition('tuname', $values['email'], '=')
                ->execute()
                ->fetchField();                

    if ($mailid == $values['email'] || $mailid2 == $values['email']) {
        $form_state->setErrorByName('email', t('E-mail ' . $values['email'] . ' has been registered already '));
    }

    $mailid = $values['email'];
    $email = '\"' . $mailid . '\"';
    $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
    // Run the preg_match() function on regex against the email address
    if (preg_match($regex, $values['email'])) {
        //echo $email . \" is a valid email. We can accept it.\";
    } else {
         $form_state->setErrorByName('mailid', t('E-mail ' . $values['email'] . ' is not a valid email '));
    }

    if (!empty($values['mobile']) && (!is_numeric($values['mobile']))) {
         $form_state->setErrorByName('mobile', t('Phone number ' . $values['mobile'] . ' is not a valid.'));
    }



    }

    public function submitForm(array &$form, FormStateInterface $form_state) {

        global $base_url;
        $values = $form_state->getValues();
        //DbTransaction
        $transaction = db_transaction();

        $signuppk = db_insert('tfrtsignup')
                ->fields(array(
                    'firstname' =>  $values['username'],
                    'lastname' =>  $values['lastname'],
                    'mailid' => $values['email'],
                    'mobile' => $values['mobile'],
                    'company' => $values['company'],
                    'mailurl' => md5($values['email'] . $values['username']),
                ))
                ->execute();
        if (!$signuppk) {
            $rb = 'YES';
        }


        \Drupal\user\Entity\User::create([
            'name' => $values['username'],
            'pass' => $values['userpassword'],
            'mail' => $values['email'],
//            'status' => 1,
            'init' => $values['email'],
        ])->save();

        if ($rb == 'YES') {
            $transaction->rollback();
        }

        $form_values = $form_state->getValues();

         
       $module = 'signup';
       $key = 'signup_message';

       // Specify 'to' and 'from' addresses.
       $to = $values['email'];
       $from = $this->config('system.site')->get('mail');
       // "params" loads in additional context for email content completion in
       // hook_mail(). In this case, we want to pass in the values the user entered
       // into the form, which include the message body in $form_values['message'].
       $params = $form_values;
       $params['signuppk'] = $signuppk;

       $language_code = $this->languageManager->getDefaultLanguage()->getId();

       $send_now = TRUE;
       // Send the mail, and check for success. Note that this does not guarantee
       // message delivery; only that there were no PHP-related issues encountered
       // while sending.
       $result = $this->mailManager->mail($module, $key, $to, $language_code, $params, $from, $send_now);

       if ($result['result'] == TRUE) {
           $this->messenger()->addMessage(t('Signup is success full. To activate your account please check your mail'));
       } else {
           $this->messenger()->addMessage(t('There was a problem sending your mail and it was not sent.'), 'error');
       }
    }

}
