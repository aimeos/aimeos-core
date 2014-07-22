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

			if ( isset($entry->{'text.id'}) ) {	$item->setId( $entry->{'text.id'} ); }
			if ( isset($entry->{'text.typeid'} ) ) { $item->setTypeId( $entry->{'text.typeid'} ); }
			if ( isset($entry->{'text.domain'} ) ) { $item->setDomain( $entry->{'text.domain'} ); }
			if ( isset($entry->{'text.status'} ) ) { $item->setStatus( $entry->{'text.status'} ); }
			if ( isset($entry->{'text.languageid'} ) && $entry->{'text.languageid'} != '' ) { $item->setLanguageId( $entry->{'text.languageid'} ); }

			if ( isset( $entry->{'text.label'} ) && $entry->{'text.label'} != '' ) {
				$label = mb_strcut( $entry->{'text.label'}, 0, 255 );
			} else if( isset( $entry->{'text.content'} ) && $entry->{'text.content'} != '' ) {
				$label = mb_strcut( $entry->{'text.content'}, 0, 255 );
			}
			$item->setLabel( trim( preg_replace( array( "/(<br>|\r|\n)+/", '/  +/' ), ' ', $label ) ) );

			if ( isset($entry->{'text.content'} ) ) {
				$item->setContent( trim( preg_replace( "/(<br>|\r|\n)+$/", '', $entry->{'text.content'} ) ) );
			}

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$this->_clearCache( $ids );

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
		$ids = (array) $params->items;
		$context = $this->_getContext();
		$manager = $this->_getManager();


		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );

		foreach( $manager->searchItems( $search ) as $id => $item ) {
			$idList[ $item->getDomain() ][] = $id;
		}

		$manager->deleteItems( $ids );


		foreach( $idList as $domain => $domainIds )
		{
			$manager = MShop_Factory::createManager( $context, $domain . '/list' );

			$search = $manager->createSearch();
			$expr = array(
				$search->compare( '==', $domain.'.list.refid', $domainIds ),
				$search->compare( '==', $domain.'.list.domain', 'text' )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSortations( array( $search->sort( '+', $domain.'.list.id' ) ) );

			$start = 0;

			do
			{
				$result = $manager->searchItems( $search );
				$manager->deleteItems( array_keys( $result ) );

				$count = count( $result );
				$start += $count;
				$search->setSlice( $start );
			}
			while( $count >= $search->getSliceSize() );
		}


		$this->_clearCache( $ids );

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
