<?php

namespace Drupal\productmaster;

Class Product_utils {
    
    static function productmaster_get_producttypes($codetype){
        $result= db_select('tragcodevalues','fc')
                ->fields('fc',array('code','codetype','description'))
                ->condition('codetype',$codetype,'=')
                ->execute()
                ->fetchAll();
       foreach($result as $key){
           $matches[$key->code]=$key->description;
       }
       return $matches;
        
    }
    
}
