<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJs cache controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Admin_Cache_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the cache controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Admin_Cache' );

		$this->_manager = MAdmin_Cache_Manager_Factory::createManager( $context );
	}


	/**
	 * Returns the service description of the class.
	 * It describes the class methods and its parameters including their types
	 *
	 * @return array Associative list of class/method names, their parameters and types
	 */
	public function getServiceDescription()
	{
		$list = parent::getServiceDescription();


		$list['Admin_Cache.flush'] = array(
			"parameters" => array(
				array( "type" => "string","name" => "site","optional" => false ),
			),
			"returns" => "array",
		);

		return $list;
	}


	/**
	 * Executes tasks after processing the items.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function flush( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site' ) );
		$this->_setLocale( $params->site );

		$this->_getContext()->getCache()->flush();

		return array(
			'success' => true,
		);
	}


	/**
	 * Creates a new text item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the text properties
	 */
	public function saveItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $this->_createItem( (array) $entry );
			$this->_manager->saveItem( $item );
		}

		return array(
			'items' => $params->items,
			'success' => true,
		);
	}


	/**
	 * Creates a new cache item and sets the properties from the given array.
	 *
	 * @param array $entry Associative list of name and value properties using the "cache" prefix
	 * @return MAdmin_Cache_Item_Interface Cache item
	 */
	protected function _createItem( array $entry )
	{
		$item = $this->_manager->createItem();

		foreach( $entry as $name => $value )
		{
			switch( $name )
			{
				case 'cache.id': $item->setId( $value ); break;
				case 'cache.value': $item->setValue( $value ); break;
				case 'cache.tags': $item->setTags( (array) $value ); break;
				case 'cache.expire':
					if( $value != '' )
					{
						$value = str_replace( 'T', ' ', $value );
						$entry->{'cache.expire'} = $value;
						$item->setTimeExpire( $value );
					}
					break;
			}
		}

		return $item;
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function _getManager()
	{
		return $this->_manager;
	}
}
