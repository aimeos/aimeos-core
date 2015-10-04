<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Frontend
 */


/**
 * Default implementation of the service frontend controller.
 *
 * @package Controller
 * @subpackage Frontend
 */
class Controller_Frontend_Service_Standard
	extends Controller_Frontend_Base
	implements Controller_Frontend_Service_Iface
{
	private $items = array();
	private $providers = array();


	/**
	 * Returns the service items that are available for the service type and the content of the basket.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param MShop_Order_Item_Base_Iface $basket Basket of the user
	 * @param array $ref List of domains for which the items referenced by the services should be fetched too
	 * @return array List of service items implementing MShop_Service_Item_Iface with referenced items
	 * @throws Exception If an error occurs
	 */
	public function getServices( $type, MShop_Order_Item_Base_Iface $basket,
		$ref = array( 'media', 'price', 'text' ) )
	{
		if( isset( $this->items[$type] ) ) {
			return $this->items[$type];
		}

		$serviceManager = MShop_Factory::createManager( $this->getContext(), 'service' );

		$search = $serviceManager->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'service.type.domain', 'service' ),
			$search->compare( '==', 'service.type.code', $type ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'service.position' ) ) );

		$this->items[$type] = $serviceManager->searchItems( $search, $ref );


		foreach( $this->items[$type] as $id => $service )
		{
			try
			{
				$provider = $serviceManager->getProvider( $service );

				if( $provider->isAvailable( $basket ) ) {
					$this->providers[$type][$id] = $provider;
				} else {
					unset( $this->items[$type][$id] );
				}
			}
			catch( MShop_Service_Exception $e )
			{
				$msg = sprintf( 'Unable to create provider "%1$s" for service with ID "%2$s"', $service->getCode(), $id );
				$this->getContext()->getLogger()->log( $msg, MW_Logger_Base::WARN );
			}
		}

		return $this->items[$type];
	}


	/**
	 * Returns the list of attribute definitions which must be used to render the input form where the customer can
	 * enter or chose the required data necessary by the service provider.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param string $serviceId Identifier of one of the service option returned by getService()
	 * @param MShop_Order_Item_Base_Iface $basket Basket object
	 * @return array List of attribute definitions implementing MW_Common_Criteria_Attribute_Iface
	 * @throws Controller_Frontend_Service_Exception If no active service provider for this ID is available
	 * @throws MShop_Exception If service provider isn't available
	 * @throws Exception If an error occurs
	 */
	public function getServiceAttributes( $type, $serviceId, MShop_Order_Item_Base_Iface $basket )
	{
		if( isset( $this->providers[$type][$serviceId] ) ) {
			return $this->providers[$type][$serviceId]->getConfigFE( $basket );
		}

		$item = $this->getServiceItem( $type, $serviceId );
		$serviceManager = MShop_Factory::createManager( $this->getContext(), 'service' );

		return $serviceManager->getProvider( $item )->getConfigFE( $basket );
	}


	/**
	 * Returns the price of the service.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param string $serviceId Identifier of one of the service option returned by getService()
	 * @param MShop_Order_Item_Base_Iface $basket Basket with products
	 * @return MShop_Price_Item_Iface Price item
	 * @throws Controller_Frontend_Service_Exception If no active service provider for this ID is available
	 * @throws MShop_Exception If service provider isn't available
	 * @throws Exception If an error occurs
	 */
	public function getServicePrice( $type, $serviceId, MShop_Order_Item_Base_Iface $basket )
	{
		if( isset( $this->providers[$type][$serviceId] ) ) {
			return $this->providers[$type][$serviceId]->calcPrice( $basket );
		}

		$item = $this->getServiceItem( $type, $serviceId );
		$serviceManager = MShop_Factory::createManager( $this->getContext(), 'service' );

		return $serviceManager->getProvider( $item )->calcPrice( $basket );
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
		if( isset( $this->providers[$type][$serviceId] ) ) {
			return $this->providers[$type][$serviceId]->checkConfigFE( $attributes );
		}

		$item = $this->getServiceItem( $type, $serviceId );
		$serviceManager = MShop_Factory::createManager( $this->getContext(), 'service' );

		return $serviceManager->getProvider( $item )->checkConfigFE( $attributes );
	}


	/**
	 * Returns the service item specified by its type and ID.
	 *
	 * @param string $type Service type, e.g. "delivery" (shipping related) or "payment" (payment related)
	 * @param string $serviceId Identifier of the service option chosen by the customer
	 * @throws Controller_Frontend_Service_Exception If no active service provider for this ID is available
	 */
	protected function getServiceItem( $type, $serviceId )
	{
		$serviceManager = MShop_Factory::createManager( $this->getContext(), 'service' );

		$search = $serviceManager->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'service.id', $serviceId ),
			$search->compare( '==', 'service.type.domain', 'service' ),
			$search->compare( '==', 'service.type.code', $type ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$items = $serviceManager->searchItems( $search, array( 'price' ) );

		if( ( $item = reset( $items ) ) === false )
		{
			$msg = sprintf( 'Service item for type "%1$s" and ID "%2$s" not found', $type, $serviceId );
			throw new Controller_Frontend_Service_Exception( $msg );
		}

		return $item;
	}
}
