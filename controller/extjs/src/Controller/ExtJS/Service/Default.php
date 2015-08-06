<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
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


	/**
	 * Initializes the service controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Service' );
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
		$manager = $this->_getManager();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $manager->createItem();
			$item->fromArray( $this->_transformValues( (array) $entry ) );
			$manager->saveItem( $item );
			$ids[] = $item->getId();
		}

		$this->_clearCache( $ids );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$result = $manager->searchItems( $search );

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
		if( $this->_manager === null ) {
			$this->_manager = MShop_Factory::createManager( $this->_getContext(), 'service' );
		}

		return $this->_manager;
	}


	/**
	 * Returns the prefix for searching items
	 *
	 * @return string MShop search key prefix
	 */
	protected function _getPrefix()
	{
		return 'service';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param stdClass $entry Entry object from ExtJS
	 * @return stdClass Modified object
	 */
	protected function _transformValues( stdClass $entry )
	{
		if( isset( $entry->{'service.config'} ) ) {
			$entry->{'service.config'} = (array) $entry->{'service.config'};
		}

		return $entry;
	}
}