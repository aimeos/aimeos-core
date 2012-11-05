<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 * @version $Id: Default.php 14265 2011-12-11 16:57:33Z nsendetzky $
 */


/**
 * ExtJS admin job controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Admin_Job_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the attribute controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Admin_Job' );

		$this->_manager = MAdmin_Job_Manager_Factory::createManager( $context );
	}


	/**
	 * Creates a new job item or updates existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the attribute properties
	 * @return array Associative list with items and success value
	 */
	public function saveItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$ids = array();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			if( !is_object( $entry ) ) {
				throw new Controller_ExtJS_Exception( 'Given item is not of type "object"' );
			}

			$item = $this->_manager->createItem();

			if( isset( $entry->{'job.id'} ) ) { $item->setId( $entry->{'job.id'} ); }
			if( isset( $entry->{'job.label'} ) ) { $item->setLabel( $entry->{'job.label'} ); }
			if( isset( $entry->{'job.method'} ) ) { $item->setMethod( $entry->{'job.method'} ); }
			if( isset( $entry->{'job.status'} ) ) { $item->setStatus( $entry->{'job.status'} ); }
			if( isset( $entry->{'job.parameter'} ) ) { $item->setParameter( $entry->{'job.parameter'} ); }
			if( isset( $entry->{'job.result'} ) ) { $item->setResult( $entry->{'job.result'} ); }

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'job.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $this->_manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return mixed Manager object
	 */
	protected function _getManager()
	{
		return $this->_manager;
	}
}
