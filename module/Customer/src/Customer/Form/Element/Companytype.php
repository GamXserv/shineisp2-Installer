<?php

namespace Customer\Form\Element;

use Customer\Service\CompanytypeService;
use Zend\Form\Element\Select;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\I18n\Translator\Translator;

class Companytype extends Select implements ServiceLocatorAwareInterface {
	protected $serviceLocator;
	protected $translator;
	protected $companytype;
	public function __construct(CompanytypeService $companytypeService, \Zend\Mvc\I18n\Translator $translator) {
		parent::__construct ();
		$this->companytype = $companytypeService;
		$this->translator = $translator;
	}
	public function init() {
		$data = array ();
		
		$records = $this->companytype->findAll ();
		$data [''] = "";
		
		foreach ( $records as $record ) {
			$data [$record->getId ()] = $this->translator->translate ( $record->getName () );
		}
		asort ( $data );
		$this->setValueOptions ( $data );
	}
	public function setServiceLocator(ServiceLocatorInterface $sl) {
		$this->serviceLocator = $sl;
	}
	public function getServiceLocator() {
		return $this->serviceLocator;
	}
}
