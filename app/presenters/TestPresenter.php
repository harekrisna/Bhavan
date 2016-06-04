<?php

namespace App\Presenters;

use Nette,
	App\Model;
use Nette\Utils\Finder;
use Nette\Utils\Image;
use Tracy\Debugger;

class TestPresenter extends BasePresenter
{
 private function title2pagename($title) {
     $title = preg_replace('/[^a-zA-Z0-9\/\.\_\-]+/u', '', $title);
    return $title;
}
	public function renderDefault()
	{   
        $string = "file_100.jpg";
        var_dump(pathinfo($string));
        
		$matches = array();
		preg_match('/_([0-9]+)$/', $string, $matches);
		if($matches != array()) {
            $index = $matches[1];    
            $new_filename = preg_replace('/_([0-9]+)$/', '_'.++$index, $string);
        }
        
        
	}
	
	protected function createComponentTestForm(){
	   	$form = new Nette\Application\UI\Form();
	
      	$form->addUpload('galery_image', 'Obrázek galerie:');
                
        $form->addSubmit('insert', 'Uložit')
		     ->onClick[] = array($this, 'testFormInsert');
		     
        return $form;
    }
    
        public function testFormInsert(\Nette\Forms\Controls\SubmitButton $button)	{

        $form = $button->form;
        $values = $form->getValues();
        Debugger::FireLog($_FILES);
		Debugger::FireLog($values['galery_image']);
    }

    
	public function renderTest() {
		echo phpinfo();
	}
}
