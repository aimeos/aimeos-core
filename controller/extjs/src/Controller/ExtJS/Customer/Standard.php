<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS customer controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Customer_Standard
	extends Controller_ExtJS_Base
	implements Controller_ExtJS_Common_Iface
{
	private $manager = null;


	/**
	 * Initializes the customer controller.
	 *
	 * @param MShop_Context_Item_Iface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Iface $context )
	{
		parent::__construct( $context, 'Customer' );
	}


	/**
	 * Creates a new item or updates an existing one or a list thereof
	 *
	 * @param stdClass $params Associative array containing the item properties
	 * @return array Associative array including items and status for ExtJS
	 */
	public function saveItems( stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'items' ) );
		$this->setLocale( $params->site );

		$ids = array();
		$manager = $this->getManager();
		$entries = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $entries as $entry )
		{
			if( isset( $entry->{'customer.id'} ) && $entry->{'customer.id'} !== '' ) {
				$item = $manager->getItem( $entry->{'customer.id'} );
			} else {
				$item = $manager->createItem();
			}

			$item->fromArray( (array) $this->transformValues( $entry ) );

			$manager->saveItem( $item );
			$ids[] = $item->getId();
		}

		return $this->getItems( $ids, $this->getPrefix() );
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Iface Manager object
	 */
	protected function getManager()
	{
		if( $this->manager === null ) {
			$this->manager = MShop_Factory::createManager( $this->getContext(), 'customer' );
		}

		return $this->manager;
	}


	/**
	 * Returns the prefix for searching items
	 *
	 * @return string MShop search key prefix
	 */
	protected function getPrefix()
	{
		return 'customer';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param stdClass $entry Entry object from ExtJS
	 * @return stdClass Modified object
	 */
	protected function transformValues( stdClass $entry )
	{
		if( isset( $entry->{'customer.birthday'} ) )
		{
			if( $entry->{'customer.birthday'} != '' ) {
				$entry->{'customer.birthday'} = substr( $entry->{'customer.birthday'}, 0, 10 );
			} else {
				$entry->{'customer.birthday'} = null;
			}
		}

		if( isset( $entry->{'customer.dateverified'} ) )
		{
			if( $entry->{'customer.dateverified'} != '' ) {
				$entry->{'customer.dateverified'} = substr( $entry->{'customer.dateverified'}, 0, 10 );
			} else {
				$entry->{'customer.dateverified'} = null;
			}
		}

		return $entry;
	}
}
