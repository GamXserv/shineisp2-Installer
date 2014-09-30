<?php

namespace Customer\View\Helper;

use Zend\View\Helper\AbstractHelper;

class MapHelper extends AbstractHelper {
	public function __invoke($address) {
		$coords = array ();
		
		if (! empty ( $address )) {
			foreach ( $address as $item ) {
				if ($item->getLatitude () && $item->getLongitude ()) {
					$coords [] = array (
							'lat' => $item->getLatitude (),
							'lng' => $item->getLongitude () 
					);
				}
			}
		}
		
		return $this->view->render ( 'customer/partial/map', array (
				'coords' => $coords 
		) );
	}
}