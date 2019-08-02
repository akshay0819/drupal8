<?php

namespace Drupal\first_module\Controller;

use Drupal\Core\Controller\ControllerBase;
//use \Mpdf\Mpdf;

class FirstModulePrint extends ControllerBase {

  public function test_page_print(){


$this->settings = $my_config = \Drupal::config('pdf_using_mpdf.settings')->get('pdf_using_mpdf');

    $renderable = [
  '#theme' => 'commentsview',
      '#title' => 'Test Value',
      '#description' => $result,
];
$rendered = \Drupal::service('renderer')->render($renderable);
       $html = $rendered;
       // drupal_set_message($this->settings['pdf_footer']);
        
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [190, 236],
            'orientation' => 'L'
        ]);
        $mpdf->SetHTMLHeader($this->settings['pdf_header']);
        $mpdf->SetHTMLFooter($this->settings['pdf_footer']);
        $mpdf->SetJS('this.print();');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
        
            return array(
      '#type' => 'markup',
      '#markup' => t($this->settings['pdf_set_title']),
    );
  }
}