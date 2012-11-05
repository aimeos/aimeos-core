<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 * @version $Id: Default.php 1116 2012-08-13 08:17:32Z nsendetzky $
 */


/**
 * Default implementation of the service frontend controller.
 *
 * @package Controller
 * @subpackage Frontend
 */
class Controller_Frontend_Service_Default
	extends Controller_Frontend_Abstract
	implements Controller_Frontend_Service_Interface
{
	private $_serviceManager;
	private $_items = array();
	private $_providers = array();


	/**
	 * Initializes the frontend controller.
	 *
	 * @param MShop_Context_Item_Interface $context Object storing the required instances for managing databases
	 *  connections, logger, session, etc.
	 * @throws Exception If an error occurs
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$this->_serviceManager = MShop_Service_Manager_Factory::createManager( $context );
	}


	/**
	 * Returns the service items that are available for the service type and the content of the basket.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param MShop_Order_Item_Base_Interface $basket Basket of the user
	 * @param array $ref List of domains for which the items referenced by the services should be fetched too
	 * @return array List of service items implementing MShop_Service_Item_Interface with referenced items
	 * @throws Exception If an error occurs
	 */
	public function getServices( $type, MShop_Order_Item_Base_Interface $basket,
		$ref = array( 'media', 'price', 'text' ) )
	{
		if( isset( $this->_items[$type] ) ) {
			return $this->_items[$type];
		}

		$search = $this->_serviceManager->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'service.type.domain', 'service' ),
			$search->compare( '==', 'service.type.code', $type ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$this->_items[$type] = $this->_serviceManager->searchItems( $search, $ref );


		foreach( $this->_items[$type] as $id => $service )
		{
			try
			{
				$provider = $this->_serviceManager->getProvider( $service );

				if( $provider->isAvailable( $basket ) ) {
					$this->_providers[$type][$id] = $provider;
				}
			}
			catch( MShop_Service_Exception $e )
			{
				$str = 'Unable to create provider "%1$s" for service with ID "%2$s"';
				$msg = sprintf( $str, $service->getCode(), $id );
				$this->_getContext()->getLogger()->log( $msg, MW_Logger_Abstract::WARN );
			}
		}

		return $this->_items[$type];
	}


	/**
	 * Returns the list of attribute definitions which must be used to render the input form where the customer can
	 * enter or chose the required data necessary by the service provider.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param string $serviceId Identifier of one of the service option returned by getService()
	 * @return array List of attribute definitions implementing MW_Common_Criteria_Attribute_Interface
	 * @throws Controller_Frontend_Service_Exception If no active service provider for this ID is available
	 * @throws MShop_Exception If service provider isn't available
	 * @throws Exception If an error occurs
	 */
	public function getServiceAttributes( $type, $serviceId )
	{
		if( isset( $this->_providers[$type][$serviceId] ) ) {
			return $this->_providers[$type][$serviceId]->getConfigFE();
		}

		$item = $this->_getServiceItem( $type, $serviceId );
		return $this->_serviceManager->getProvider( $item )->getConfigFE();
	}


	/**
	 * Returns a list of attributes that are invalid.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param string $serviceId Identifier of the service option chosen by the customer
	 * @param array $attributes List of key/value pairs with name of the attribute from attribute definition object as
	 * 	key and the string entered by the customer as value
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 * @throws Controller_Frontend_Service_Exception If no active service provider for this ID is available
	 */
	public function checkServiceAttributes( $type, $serviceId, array $attributes )
	{
		if( isset( $this->_providers[$type][$serviceId] ) ) {
			return $this->_providers[$type][$serviceId]->checkConfigFE( $attributes );
		}

		$item = $this->_getServiceItem( $type, $serviceId );
		return $this->_serviceManager->getProvider( $item )->checkConfigFE( $attributes );
	}


	/**
	 * Returns the service item specified by its type and ID.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param string $serviceId Identifier of the service option chosen by the customer
	 * @throws Controller_Frontend_Service_Exception If no active service provider for this ID is available
	 */
	protected function _getServiceItem( $type, $serviceId )
	{
		$search = $this->_serviceManager->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'service.id', $serviceId ),
			$search->compare( '==', 'service.type.domain', 'service' ),
			$search->compare( '==', 'service.type.code', $type ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$items = $this->_serviceManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false )
		{
			$msg = sprintf( 'No service item for type "%1$s" and ID "%2$" available', $type, $serviceId );
			throw new Controller_Frontend_Service_Exception( $msg );
		}

		return $item;
	}
}
