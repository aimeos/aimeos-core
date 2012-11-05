<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 * @version $Id: Default.php 14265 2011-12-11 16:57:33Z nsendetzky $
 */


/**
 * ExtJS product tag type controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Product_Tag_Type_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the product tag type controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Product_Tag_Type' );

		$manager = MShop_Product_Manager_Factory::createManager( $context );
		$tagManager = $manager->getSubManager( 'tag' );
		$this->_manager = $tagManager->getSubManager( 'type' );
	}


	/**
	 * Creates a new product tag type item or updates an existing one or a tag thereof.
	 *
	 * @param stdClass $params Associative array containing the product tag type properties
	 */
	public function saveItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$ids = array();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $this->_manager->createItem();

			if( isset( $entry->{'product.tag.type.id'} ) ) { $item->setId( $entry->{'product.tag.type.id'} ); }
			if( isset( $entry->{'product.tag.type.code'} ) ) { $item->setCode( $entry->{'product.tag.type.code'} ); }
			if( isset( $entry->{'product.tag.type.domain'} ) ) { $item->setDomain( $entry->{'product.tag.type.domain'} ); }
			if( isset( $entry->{'product.tag.type.label'} ) ) { $item->setLabel( $entry->{'product.tag.type.label'} ); }
			if( isset( $entry->{'product.tag.type.status'} ) ) { $item->setStatus( $entry->{'product.tag.type.status'} ); }

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.tag.type.id', $ids ) );
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
