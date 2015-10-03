<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS order base controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Order_Base_Default
	extends Controller_ExtJS_Base
	implements Controller_ExtJS_Common_Iface
{
	private $manager = null;


	/**
	 * Initializes the order base controller.
	 *
	 * @param MShop_Context_Item_Iface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Iface $context )
	{
		parent::__construct( $context, 'Order_Base' );
	}


	/**
	 * Creates a new order base item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the order base properties
	 */
	public function saveItems( stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'items' ) );

		$ids = array();
		$manager = $this->getManager();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$langid = ( isset( $entry->{'order.base.languageid'} ) ? $entry->{'order.base.languageid'} : null );
			$currencyid = ( isset( $entry->{'order.base.currencyid'} ) ? $entry->{'order.base.currencyid'} : null );

			$this->setLocale( $params->site, $langid, $currencyid );

			$item = $manager->createItem();
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
			$this->manager = MShop_Factory::createManager( $this->getContext(), 'order/base' );
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
		return 'order.base';
	}
}
