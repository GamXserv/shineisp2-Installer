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
* @package Product
* @subpackage Service
* @author Michelangelo Turillo <mturillo@shinesoftware.com>
* @copyright 2014 Michelangelo Turillo.
* @license http://www.opensource.org/licenses/bsd-license.php BSD License
* @link http://shinesoftware.com
* @version @@PACKAGE_VERSION@@
*/
namespace Product\Service;

use Product\Entity\ProductAttributesInterface;
use Zend\EventManager\EventManager;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

class ProductAttributeService implements ProductAttributeServiceInterface, EventManagerAwareInterface {
	protected $tableGateway;
	protected $translator;
	protected $eventManager;
	public function __construct(TableGateway $tableGateway, \Zend\Mvc\I18n\Translator $translator) {
		$this->tableGateway = $tableGateway;
		$this->translator = $translator;
	}
	public function fetchAllToArray() {
		$aData = $this->tableGateway->fetchAll ()->toArray ();
		return $aData;
	}
	
	/**
	 * @inheritDoc
	 */
	public function findAll() {
		$records = $this->tableGateway->select ( function (\Zend\Db\Sql\Select $select) {
		} );
		
		return $records;
	}
	
	/**
	 * @inheritDoc
	 */
	public function findUserDefined($is_user_defined = true) {
		$records = $this->tableGateway->select ( function (\Zend\Db\Sql\Select $select) use($is_user_defined) {
			$select->where ( array (
					'is_user_defined' => $is_user_defined 
			) );
			$select->order ( 'name' );
		} );
		
		return $records ? $records : null;
	}
	
	/**
	 * @inheritDoc
	 */
	public function findbyName($name) {
		$records = $this->tableGateway->select ( function (\Zend\Db\Sql\Select $select) use($name) {
			$select->where ( array (
					'name' => $name 
			) );
		} );
		return $records ? $records->current () : null;
	}
	
	/**
	 * @inheritDoc
	 */
	public function findbyIdx(array $idx) {
		$records = $this->tableGateway->select ( function (\Zend\Db\Sql\Select $select) use($idx) {
			$select->where ( array (
					'id' => $idx 
			) );
		} );
		return $records ? $records : null;
	}
	
	/**
	 * @inheritDoc
	 */
	public function find($id) {
		if (! is_numeric ( $id )) {
			return false;
		}
		$rowset = $this->tableGateway->select ( array (
				'id' => $id 
		) );
		$row = $rowset->current ();
		
		return $row;
	}
	
	/**
	 * @inheritDoc
	 */
	public function delete($id) {
		$this->tableGateway->delete ( array (
				'id' => $id 
		) );
	}
	
	/**
	 * @inheritDoc
	 */
	public function save(\Product\Entity\ProductAttributes $record) {
		$hydrator = new ClassMethods ();
		
		// extract the data from the object
		$data = $hydrator->extract ( $record );
		$id = ( int ) $record->getId ();
		
		$this->getEventManager ()->trigger ( __FUNCTION__ . '.pre', null, array (
				'data' => $data 
		) ); // Trigger an event
		
		$data ['filters'] = ! empty ( $data ['filters'] ) ? json_encode ( $data ['filters'] ) : null;
		$data ['filemimetype'] = ! empty ( $data ['filemimetype'] ) ? json_encode ( $data ['filemimetype'] ) : null;
		$data ['source_model'] = null;
		
		switch ($data ['input']) {
			
			case "text" :
				
				break;
			
			case "textarea" :
				$data ['type'] = "text";
				break;
			
			case "select" :
				
				break;
			
			case "file" : // File upload standard settings
				$data ['filters'] = array (
						array (
								'name' => 'File\RenameUpload',
								'options' => array (
										'target' => PUBLIC_PATH . $data ['filetarget'] . '/',
										'overwrite' => true,
										'use_upload_name' => true 
								) 
						) 
				);
				
				@mkdir ( PUBLIC_PATH . $data ['filetarget'], 0777, true );
				
				$data ['validators'] = array (
						array (
								'name' => 'File\UploadFile',
								'filesize' => array (
										'max' => $data ['filesize'] 
								) 
						) 
				);
				
				if (! empty ( $data ['filemimetype'] )) {
					$mimeTypes = json_decode ( $data ['filemimetype'], true );
					foreach ( $mimeTypes as $mimeType ) {
						$filemimetypes [] = array (
								'mimeType' => $mimeType 
						);
					}
					
					$data ['validators'] [0] ['filemimetype'] = $filemimetypes;
				}
				
				$data ['filters'] = json_encode ( $data ['filters'] );
				$data ['validators'] = json_encode ( $data ['validators'] );
				$data ['source_model'] = "Zend\Form\Element\File";
				break;
		}
		
		if ($id == 0) {
			unset ( $data ['id'] );
			
			// Save the data
			$this->tableGateway->insert ( $data );
			
			// Get the ID of the record
			$id = $this->tableGateway->getLastInsertValue ();
		} else {
			
			$rs = $this->find ( $id );
			
			if (! empty ( $rs )) {
				
				// Save the data
				$this->tableGateway->update ( $data, array (
						'id' => $id 
				) );
			} else {
				throw new \Exception ( 'Record ID does not exist' );
			}
		}
		
		$record = $this->find ( $id );
		$this->getEventManager ()->trigger ( __FUNCTION__ . '.post', null, array (
				'id' => $id,
				'data' => $data,
				'record' => $record 
		) ); // Trigger an event
		return $record;
	}
	
	/*
	 * (non-PHPdoc) @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
	 */
	public function setEventManager(EventManagerInterface $eventManager) {
		$eventManager->addIdentifiers ( get_called_class () );
		$this->eventManager = $eventManager;
	}
	
	/*
	 * (non-PHPdoc) @see \Zend\EventManager\EventsCapableInterface::getEventManager()
	 */
	public function getEventManager() {
		if (null === $this->eventManager) {
			$this->setEventManager ( new EventManager () );
		}
		
		return $this->eventManager;
	}
}