<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Price;


/**
 * ExtJs price controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Standard
	extends \Aimeos\Controller\ExtJS\Base
	implements \Aimeos\Controller\ExtJS\Common\Iface
{
	private $manager = null;


	/**
	 * Initializes the media controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context, 'Price' );
	}


	/**
	 * Retrieves all items matching the given criteria.
	 *
	 * @param \stdClass $params Associative array containing the parameters
	 * @return array List of associative arrays with item properties, total number of items and success property
	 */
	public function searchItems( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site' ) );
		$this->setLocale( $params->site );

		$total = 0;
		$search = $this->initCriteria( $this->getManager()->createSearch(), $params );

		if( isset( $params->domain ) && isset( $params->parentid )
			&& ( !isset( $params->options->showall ) || $params->options->showall == false ) )
		{
			$listManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $params->domain . '/lists' );
			$criteria = $listManager->createSearch();

			$expr = array();
			$expr[] = $criteria->compare( '==', $params->domain . '.lists.parentid', $params->parentid );
			$expr[] = $criteria->compare( '==', $params->domain . '.lists.domain', 'price' );
			$criteria->setConditions( $criteria->combine( '&&', $expr ) );

			$result = $listManager->searchItems( $criteria );

			$ids = array();
			foreach( $result as $items ) {
				$ids[] = $items->getRefId();
			}

			$expr = array();
			$expr[] = $search->compare( '==', 'price.id', $ids );
			$expr[] = $search->getConditions();
			$search->setConditions( $search->combine( '&&', $expr ) );
		}

		$items = $this->getManager()->searchItems( $search, array(), $total );

		return array(
			'items' => $this->toArray( $items ),
			'total' => $total,
			'success' => true,
		);
	}


	/**
	 * Returns the service description of the class.
	 * It describes the class methods and its parameters including their types
	 *
	 * @return array Associative list of class/method names, their parameters and types
	 */
	public function getServiceDescription()
	{
		return array(
			'Price.deleteItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Price.saveItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Price.searchItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "condition", "optional" => true ),
					array( "type" => "integer", "name" => "start", "optional" => true ),
					array( "type" => "integer", "name" => "limit", "optional" => true ),
					array( "type" => "string", "name" => "sort", "optional" => true ),
					array( "type" => "string", "name" => "dir", "optional" => true ),
					array( "type" => "string", "name" => "domain", "optional" => true ),
					array( "type" => "string", "name" => "label", "optional" => true ),
					array( "type" => "integer", "name" => "parentid", "optional" => true ),
					array( "type" => "array", "name" => "options", "optional" => true ),
				),
				"returns" => "array",
			),
		);
	}

	/**
	 * Deletes an item or a list of items.
	 *
	 * @param \stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function deleteItems( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'items' ) );
		$this->setLocale( $params->site );

		$idList = array();
		$ids = (array) $params->items;
		$context = $this->getContext();
		$manager = $this->getManager();


		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'price.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );

		foreach( $manager->searchItems( $search ) as $id => $item ) {
			$idList[$item->getDomain()][] = $id;
		}

		$manager->deleteItems( $ids );


		foreach( $idList as $domain => $domainIds )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $context, $domain . '/lists' );

			$search = $manager->createSearch();
			$expr = array(
				$search->compare( '==', $domain . '.lists.refid', $domainIds ),
				$search->compare( '==', $domain . '.lists.domain', 'price' )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSortations( array( $search->sort( '+', $domain . '.lists.id' ) ) );

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
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager()
	{
		if( $this->manager === null ) {
			$this->manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'price' );
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
		return 'price';
	}
}
