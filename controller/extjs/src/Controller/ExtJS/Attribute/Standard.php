<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Attribute;


/**
 * ExtJS attribute controller for admin interfaces.
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
	 * Initializes the attribute controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context, 'Attribute' );
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
		$search->setConditions( $search->compare( '==', 'attribute.id', $ids ) );
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
				$search->compare( '==', $domain . '.lists.domain', 'attribute' )
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
			$this->manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'attribute' );
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
		return 'attribute';
	}
}
