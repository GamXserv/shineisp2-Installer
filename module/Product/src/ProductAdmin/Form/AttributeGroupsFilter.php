<?php

namespace ProductAdmin\Form;

use Zend\InputFilter\InputFilter;

class AttributeGroupsFilter extends InputFilter {
	public function __construct() {
		$this->add ( array (
				'name' => 'name',
				'required' => true,
				'filters' => array (
						array (
								'name' => 'StringTrim' 
						) 
				) 
		) );
	}
}