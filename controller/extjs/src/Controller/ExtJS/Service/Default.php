<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS product controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Service_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;
	private $_context = null;


	/**
	 * Initializes the service controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Service' );

		$this->_manager = MShop_Service_Manager_Factory::createManager( $context );
		$this->_context = $context;
	}


	/**
	 * Creates a new service item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the service properties
	 * @return array Associative list with nodes and success value
	 */
	public function saveItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$ids = array();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $this->_createItem( (array) $entry );
			$this->_manager->saveItem( $item );
			$ids[] = $item->getId();
		}

		$this->_clearCache( $ids );

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$result = $this->_manager->searchItems( $search );

		foreach( $result as $item ) {
			$this->_checkConfig( $item );
		}

		$items = $this->_toArray( $result );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	/**
	 * Creates a new service item and sets the properties from the given array.
	 *
	 * @param array $entry Associative list of name and value properties using the "service" prefix
	 * @return MShop_Service_Item_Interface Service item
	 */
	protected function _createItem( array $entry )
	{
		$item = $this->_manager->createItem();

		foreach( $entry as $name => $value )
		{
			switch( $name )
			{
				case 'service.id': $item->setId( $value ); break;
				case 'service.code': $item->setCode( $value ); break;
				case 'service.label': $item->setLabel( $value ); break;
				case 'service.typeid': $item->setTypeId( $value ); break;
				case 'service.status': $item->setStatus( $value ); break;
				case 'service.position': $item->setPosition( $value ); break;
				case 'service.provider': $item->setProvider( $value ); break;
				case 'service.config': $item->setConfig( (array) $value ); break;
			}
		}

		return $item;
	}


	/**
	 * Tests the configuration and throws an exception if it's invalid
	 *
	 * @param MShop_Service_Item_Interface $item Service item object
	 * @throws Controller_ExtJS_Exception If configuration is invalid
	 */
	protected function _checkConfig( MShop_Service_Item_Interface $item )
	{
		$msg = '';
		$provider = $this->_manager->getProvider( $item );
		$result = $provider->checkConfigBE( $item->getConfig() );

		foreach( $result as $key => $message )
		{
			if( $message !== null ) {
				$msg .= sprintf( "- %1\$s : %2\$s\n", $key, $message );
			}
		}

		if( $msg !== '' ) {
			throw new Controller_ExtJS_Exception( "Invalid configuration:\n" . $msg );
		}
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