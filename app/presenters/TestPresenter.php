<?php

namespace App\Presenters;

use Nette,
	App\Model;
use Nette\Utils\Finder;
use Nette\Utils\Image;
use Tracy\Debugger;
use Extensions\FBGallery;

class TestPresenter extends BasePresenter
{
 private function title2pagename($title) {
     $title = preg_replace('/[^a-zA-Z0-9\/\.\_\-]+/u', '', $title);
    return $title;
}
	public function renderDefault()
	{   
		if(empty($_GET['fid'])){$_GET['fid'] = '160940383943087';} // force if empty for demo
		
        $cache = array('permission' => 'y',
					'location' => 'cache', // ensure this directory has permission to read and write
					'time' => 7200);
					
		$gallery = new FBGallery($_GET['fid'],'y',$cache);        
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
