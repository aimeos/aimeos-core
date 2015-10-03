<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
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
	extends Controller_ExtJS_Base
	implements Controller_ExtJS_Common_Iface
{
	private $manager = null;


	/**
	 * Initializes the text controller.
	 *
	 * @param MShop_Context_Item_Iface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Iface $context )
	{
		parent::__construct( $context, 'Text' );
	}


	/**
	 * Deletes an item or a list of items.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function deleteItems( stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'items' ) );
		$this->setLocale( $params->site );

		$idList = array();
		$ids = (array) $params->items;
		$context = $this->getContext();
		$manager = $this->getManager();


		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );

		foreach( $manager->searchItems( $search ) as $id => $item ) {
			$idList[$item->getDomain()][] = $id;
		}

		$manager->deleteItems( $ids );


		foreach( $idList as $domain => $domainIds )
		{
			$manager = MShop_Factory::createManager( $context, $domain . '/list' );

			$search = $manager->createSearch();
			$expr = array(
				$search->compare( '==', $domain . '.list.refid', $domainIds ),
				$search->compare( '==', $domain . '.list.domain', 'text' )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSortations( array( $search->sort( '+', $domain . '.list.id' ) ) );

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


		$this->clearCache( $ids );

		return array(
				'items' => $params->items,
				'success' => true,
		);
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Iface Manager object
	 */
	protected function getManager()
	{
		if( $this->manager === null ) {
			$this->manager = MShop_Factory::createManager( $this->getContext(), 'text' );
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
		return 'text';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param stdClass $entry Entry object from ExtJS
	 * @return stdClass Modified object
	 */
	protected function transformValues( stdClass $entry )
	{
		if( isset( $entry->{'text.languageid'} ) && $entry->{'text.languageid'} === '' ) {
			$entry->{'text.languageid'} = null;
		}

		if( isset( $entry->{'text.content'} ) ) {
			$entry->{'text.content'} = trim( preg_replace( '/(<br>|\r|\n)+$/', '', $entry->{'text.content'} ) );
		}

		if( isset( $entry->{'text.label'} ) ) {
			$entry->{'text.label'} = trim( preg_replace( '/(<br>|\r|\n)+$/', '', $entry->{'text.label'} ) );
		} else if( isset( $entry->{'text.content'} ) ) {
			$entry->{'text.label'} = mb_strcut( trim( preg_replace( '/(<br>|\r|\n)+/', ' ', $entry->{'text.content'} ) ), 0, 255 );
		}

		return $entry;
	}
}
