<?php

/**
 * Copyright (c) 2014 Shine Software.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * * Redistributions of source code must retain the above copyright
 * notice, this list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright
 * notice, this list of conditions and the following disclaimer in
 * the documentation and/or other materials provided with the
 * distribution.
 *
 * * Neither the names of the copyright holders nor the names of the
 * contributors may be used to endorse or promote products derived
 * from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package Customer
 * @subpackage Factory
 * @author Michelangelo Turillo <mturillo@shinesoftware.com>
 * @copyright 2014 Michelangelo Turillo.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://shinesoftware.com
 * @version @@PACKAGE_VERSION@@
 */
namespace CustomerAdmin\Factory;

use CustomerAdmin\Controller\IndexController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use CustomerAdmin\Model\CustomerDatagrid;

class IndexControllerFactory implements FactoryInterface {
	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator        	
	 *
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$realServiceLocator = $serviceLocator->getServiceLocator ();
		$customerService = $realServiceLocator->get ( 'CustomerService' );
		$addressService = $realServiceLocator->get ( 'AddressService' );
		$contactService = $realServiceLocator->get ( 'ContactService' );
		$settings = $realServiceLocator->get ( 'SettingsService' );
		$dbAdapter = $realServiceLocator->get ( 'Zend\Db\Adapter\Adapter' );
		$datagrid = $realServiceLocator->get ( 'ZfcDatagrid\Datagrid' );
		$form = $realServiceLocator->get ( 'FormElementManager' )->get ( 'CustomerAdmin\Form\CustomerForm' );
		$formfilter = $realServiceLocator->get ( 'AdminCustomerFilter' );
		
		// prepare the datagrid to handle the custom columns and data
		$theDatagrid = new CustomerDatagrid ( $dbAdapter, $datagrid, $settings );
		$grid = $theDatagrid->getDatagrid ();
		
		return new IndexController ( $customerService, $addressService, $contactService, $form, $formfilter, $grid, $settings );
	}
}