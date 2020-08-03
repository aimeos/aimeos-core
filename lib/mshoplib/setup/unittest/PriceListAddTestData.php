<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds price-list test data and all items from other domains.
 */
class PriceListAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['CustomerAddTestData', 'PriceAddTestData'];
	}


	/**
	 * Adds price test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding price-list test data', 0 );
		$this->additional->setEditor( 'core:lib/mshoplib' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'price-list.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for price domain', $path ) );
		}

		$refKeys = [];
		foreach( $testdata['price/lists'] as $dataset ) {
			$refKeys[$dataset['domain']][] = $dataset['refid'];
		}

		$refIds = [];
		$refIds['customer'] = $this->getCustomerData( $refKeys['customer'] );

		$this->addPriceListData( $testdata, $refIds );

		$this->status( 'done' );
	}


	/**
	 * Gets required customer item ids.
	 *
	 * @param array $keys List with referenced Ids
	 * @return array $refIds List with referenced Ids
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function getCustomerData( array $keys )
	{
		$customerManager = \Aimeos\MShop::create( $this->additional, 'customer' );

		$codes = [];
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref customer are set wrong "%1$s"', $dataset ) );
			}

			$codes[] = $str;
		}

		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', $codes ) );

		$refIds = [];
		foreach( $customerManager->searchItems( $search ) as $item ) {
			$refIds['customer/' . $item->getCode()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Adds the price-list test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $refIds Associative list of domains and the keys/IDs of the inserted items
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addPriceListData( array $testdata, array $refIds )
	{
		$priceManager = \Aimeos\MShop\Price\Manager\Factory::create( $this->additional, 'Standard' );
		$priceListManager = $priceManager->getSubManager( 'lists', 'Standard' );

		$value = $ship = $domain = $code = [];
		foreach( $testdata['price/lists'] as $dataset )
		{
			$exp = explode( '/', $dataset['parentid'] );

			if( count( $exp ) != 5 ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
			}

			$domain[] = $exp[1];
			$code[] = $exp[2];
			$value[] = $exp[3];
			$ship[] = $exp[4];
		}


		$priceManager->begin();

		$this->addPriceListTypeItems( $testdata['price/lists/type'] );
		$parentIds = $this->getPriceIds( $value, $ship, $code );

		$listItem = $priceListManager->createItem();
		foreach( $testdata['price/lists'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['parentid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No price ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $refIds[$dataset['domain']][$dataset['refid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No "%1$s" ref ID found for "%2$s"', $dataset['refid'], $dataset['domain'] ) );
			}

			$listItem->setId( null );
			$listItem->setParentId( $parentIds[$dataset['parentid']] );
			$listItem->setRefId( $refIds[$dataset['domain']] [$dataset['refid']] );
			$listItem->setType( $dataset['type'] );
			$listItem->setDomain( $dataset['domain'] );
			$listItem->setDateStart( $dataset['start'] );
			$listItem->setDateEnd( $dataset['end'] );
			$listItem->setConfig( $dataset['config'] );
			$listItem->setPosition( $dataset['pos'] );
			$listItem->setStatus( $dataset['status'] );

			$priceListManager->saveItem( $listItem, false );
		}

		$priceManager->commit();
	}


	/**
	 * Returns the price IDs for the given data
	 *
	 * @param array $value Price values
	 * @param array $ship Price shipping costs
	 * @param array $codes List of price type codes
	 * @param array Associative list of identifiers as keys and price IDs as values
	 */
	protected function getPriceIds( array $value, array $ship, array $codes )
	{
		$manager = \Aimeos\MShop\Price\Manager\Factory::create( $this->additional, 'Standard' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'price.value', $value ),
			$search->compare( '==', 'price.costs', $ship ),
			$search->compare( '==', 'price.type', $codes )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$parentIds = [];
		foreach( $manager->searchItems( $search ) as $item ) {
			$parentIds['price/' . $item->getDomain() . '/' . $item->getType() . '/' . $item->getValue() . '/' . $item->getCosts()] = $item->getId();
		}

		return $parentIds;
	}


	/**
	 * Adds the price list items
	 *
	 * @param array $data Associative list of identifiers as keys and data sets as values
	 */
	protected function addPriceListTypeItems( array $data )
	{
		$manager = \Aimeos\MShop\Price\Manager\Factory::create( $this->additional, 'Standard' );
		$listManager = $manager->getSubManager( 'lists', 'Standard' );
		$listTypeManager = $listManager->getSubManager( 'type', 'Standard' );

		$listItemType = $listTypeManager->createItem();

		foreach( $data as $key => $dataset )
		{
			try
			{
				$listItemType->setId( null );
				$listItemType->setCode( $dataset['code'] );
				$listItemType->setDomain( $dataset['domain'] );
				$listItemType->setLabel( $dataset['label'] );
				$listItemType->setStatus( $dataset['status'] );

				$listTypeManager->saveItem( $listItemType );
			}
			catch( \Exception $e ) {} // Duplicate entry
		}
	}
}
