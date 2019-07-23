<?php
 
namespace Drupal\productmaster\Controller;
 
use Drupal\Core\Controller\ControllerBase;
 
class ProductmasterTheme extends ControllerBase {
  public function content() {
 
    return array(
      '#theme' => 'slabtheme',
      '#test_var' => $this->t('Test Value'),
    );
 
  }
}
