<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJs text controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Text_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the text controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Text' );

		$this->_manager = MShop_Text_Manager_Factory::createManager( $context );
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

		$ids = array();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $this->_manager->createItem();

			if ( isset($entry->{'text.id'}) ) {	$item->setId($entry->{'text.id'});}
			if ( isset($entry->{'text.typeid'}) ) { $item->setTypeId($entry->{'text.typeid'}); }
			if ( isset($entry->{'text.domain'}) ) {	$item->setDomain($entry->{'text.domain'}); }
			if ( isset($entry->{'text.content'}) ) { $item->setContent($entry->{'text.content'}); }
			if ( isset($entry->{'text.status'}) ) {	$item->setStatus($entry->{'text.status'});}
			if ( isset($entry->{'text.languageid'}) && $entry->{'text.languageid'} != '' ) {$item->setLanguageId($entry->{'text.languageid'});}

			if ( isset( $entry->{'text.label'} ) && $entry->{'text.label'} != '' ) {
				$item->setLabel( $entry->{'text.label'} );
			} else if( isset( $entry->{'text.content'} ) ) {
				$item->setLabel( substr( $entry->{'text.content'}, 0, 255 ) );
			}

			$this->_manager->saveItem( $item );
			$id = $item->getId();

			if( isset( $entry->{'isCopiedItem'} ) &&  isset( $entry->{'isCopiedItemOlDId'} ) ) {
				$this->_copyListItems( $entry->{'isCopiedItemOlDId'}, $id, 'text' );
			}

			$ids[] = $id;
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $this->_manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	/**
	* Deletes an item or a list of items.
	*
	* @param stdClass $params Associative list of parameters
	* @return array Associative list with success value
	*/
	public function deleteItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$idList = array();
		$manager = $this->_getManager();

		foreach( (array) $params->items as $id )
		{
			$idList[ $manager->getItem( $id )->getDomain() ][] = $id;
			$manager->deleteItem( $id );
		}

		foreach( $idList as $manager => $ids )
		{
			$refDomainListManager = MShop_Factory::createManager( $this->_getContext(), $manager . '/list' );

			$search = $refDomainListManager->createSearch();
			$expr = array(
				$search->compare( '==', $manager.'.list.refid', $ids ),
				$search->compare( '==', $manager.'.list.domain', 'text' )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSortations( array( $search->sort( '+', $manager.'.list.id' ) ) );

			$start = 0;

			do
			{
				$result = $refDomainListManager->searchItems( $search );

				foreach ( $result as $item ) {
					$refDomainListManager->deleteItem( $item->getId() );
				}

				$count = count( $result );
				$start += $count;
				$search->setSlice( $start );
			}
			while( $count > 0 );
		}

		return array(
				'items' => $params->items,
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
