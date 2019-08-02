<?php

namespace Drupal\batch\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
//use Drupal\Component\Utility\SafeMarkup;
use Drupal\Component\Utility\Tags;
use Drupal\Component\Utility\Unicode;

/**
 * Defines a route controller for entity autocomplete form elements.
 */
class AutocompleteController extends ControllerBase {

    /**
     * Handler for autocomplete request.
     */
    public function productAutocomplete(Request $request) {
	$results = [];
	if($input = $request->query->get('q')){
		$typed_string = Tags::explode($input);
		$typed_string = Unicode::strtolower(array_pop($typed_string));
		$query = \Drupal::database()->select('frtproduct', 'n');
		$query->fields('n', array('productdesc','productpk'));
		//$query->condition('productdesc', $this->currentUser()->id(),,'!=');
		$query->condition('productdesc', $query->escapeLike($typed_string) . '%', 'LIKE');
		$result = $query->execute()->fetchAll();
		foreach ($result as $fld) $results[] = ['value' => $fld->productdesc, 'label' => $fld->productdesc];
	}
	return new JsonResponse($results);

/*
        $matches = array();
        $string = $request->query->get('q');
        $result = db_select('frtproduct', 'n')
                ->fields('n', array('productdesc', 'productpk'))
                ->condition('productdesc', '%' . db_like($string) . '%', 'LIKE')
//                ->condition('productcode', '%' . db_like($string) . '%', 'LIKE')
                ->execute();

        foreach ($result as $row) {
            $matches[$row->productpk] = $row->productdesc;
        }
        return new JsonResponse($matches);
*/
    }

    static function getTemplateFields($templatepk) {
        $result = db_select('tragcustomtemplatefields', 'fc')
                ->fields('fc', array('slno', 'fcode', 'fname', 'ftype'))
                ->condition('customtemplatepk', $templatepk, '=')
                ->execute()
                ->fetchAll();
        return $result;
    }

    static function getTemplates() {
         $matches = array();
         $matches[] = 'Select Template';
        $result = db_select('tragcustomtemplate', 'fc')
                ->fields('fc', array('customtemplatepk', 'customtemplatename'))
                ->execute()
                ->fetchAll();
        foreach ($result as $row) {
            $matches[$row->customtemplatepk] = $row->customtemplatename;
        }
         return $matches;
    }
    
    
    public function manageAction() {

        $id = 1;

        $form = \Drupal::formBuilder()->getForm('Drupal\batch\Form\BatchForm');

//        $form['type']['#title_display'] = 'invisible';

        return [ '#theme' => 'batchtheme',
            '#form' => $form,
            '#templatefields' => 'ss',
        ];
    }

}
