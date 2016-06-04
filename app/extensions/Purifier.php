<?php
namespace Extensions;
use Nette;

class MyPurifier extends Nette\Object	{
    private $htmlPurifier;

    public function __construct()	{
   		require_once 'HTMLPurifier/HTMLPurifier.auto.php';	    
        $config = \HTMLPurifier_Config::createDefault();
        $this->htmlPurifier = new \HTMLPurifier($config);
    }

    public function purify($dirtyHtml)	{
        return $this->htmlPurifier->purify($dirtyHtml);
    }
}