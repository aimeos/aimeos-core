<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS product tag controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Product_Tag_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the product tag controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Product_Tag' );

		$manager = MShop_Product_Manager_Factory::createManager( $context );
		$this->_manager = $manager->getSubManager( 'tag' );
	}


	/**
	 * Creates a new product tag item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the product properties
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

			if( isset( $entry->{'product.tag.id'} ) ) { $item->setId( $entry->{'product.tag.id'} ); }
			if( isset( $entry->{'product.tag.typeid'} ) ) { $item->setTypeId( $entry->{'product.tag.typeid'} ); }
			if( isset( $entry->{'product.tag.label'} ) ) { $item->setLabel( $entry->{'product.tag.label'} ); }
			if( isset( $entry->{'product.tag.languageid'} ) && $entry->{'product.tag.languageid'} != '' ) {
				$item->setLanguageId( $entry->{'product.tag.languageid'} );
			}

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.tag.id', $ids ) );
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
