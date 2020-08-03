<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds text test data.
 */
class TextListAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['TextAddTestData', 'MediaAddTestData'];
	}


	/**
	 * Adds text test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding text-list test data', 0 );
		$this->additional->setEditor( 'core:lib/mshoplib' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'text-list.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for text list domain', $path ) );
		}

		$refKeys = [];
		foreach( $testdata['text/lists'] as $dataset ) {
			$refKeys[$dataset['domain']][] = $dataset['refid'];
		}

		$refIds = [];
		$refIds['media'] = $this->getMediaData( $refKeys['media'] );

		$this->addTextData( $testdata, $refIds );

		$this->status( 'done' );
	}


	/**
	 * Gets required media item ids.
	 *
	 * @param array $keys List of keys for search
	 * @return array $refIds List with referenced Ids
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function getMediaData( array $keys )
	{
		$mediaManager = \Aimeos\MShop\Media\Manager\Factory::create( $this->additional, 'Standard' );

		$urls = [];
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref media are set wrong "%1$s"', $dataset ) );
			}

			$urls[] = $str;
		}

		$search = $mediaManager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.url', $urls ) );
		$result = $mediaManager->searchItems( $search );

		$refIds = [];
		foreach( $result as $item ) {
			$refIds['media/' . $item->getUrl()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Adds the text-list test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $refIds Associative list of domains and the keys/IDs of the inserted items
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addTextData( array $testdata, array $refIds )
	{
		$textManager = \Aimeos\MShop\Text\Manager\Factory::create( $this->additional, 'Standard' );
		$textListManager = $textManager->getSubManager( 'lists', 'Standard' );
		$textListTypeManager = $textListManager->getSubmanager( 'type', 'Standard' );

		$labels = [];
		foreach( $testdata['text/lists'] as $dataset )
		{
			if( ( $pos = strpos( $dataset['parentid'], '/' ) ) === false || ( $str = substr( $dataset['parentid'], $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
			}

			$labels[] = $str;
		}

		$search = $textManager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.label', $labels ) );

		$parentIds = [];
		foreach( $textManager->searchItems( $search ) as $item ) {
			$parentIds['text/' . $item->getLabel()] = $item->getId();
		}

		$tListType = $textListTypeManager->createItem();

		$textManager->begin();

		foreach( $testdata['text/lists/type'] as $key => $dataset )
		{
			$tListType->setId( null );
			$tListType->setCode( $dataset['code'] );
			$tListType->setDomain( $dataset['domain'] );
			$tListType->setLabel( $dataset['label'] );
			$tListType->setStatus( $dataset['status'] );

			$textListTypeManager->saveItem( $tListType );
		}

		$tList = $textListManager->createItem();
		foreach( $testdata['text/lists'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['parentid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No text ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $refIds[$dataset['domain']][$dataset['refid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No "%1$s" ref ID found for "%2$s"', $dataset['refid'], $dataset['domain'] ) );
			}

			$tList->setId( null );
			$tList->setParentId( $parentIds[$dataset['parentid']] );
			$tList->setRefId( $refIds[$dataset['domain']] [$dataset['refid']] );
			$tList->setType( $dataset['type'] );
			$tList->setDomain( $dataset['domain'] );
			$tList->setDateStart( $dataset['start'] );
			$tList->setDateEnd( $dataset['end'] );
			$tList->setConfig( $dataset['config'] );
			$tList->setPosition( $dataset['pos'] );
			$tList->setStatus( $dataset['status'] );

			$textListManager->saveItem( $tList, false );
		}

		$textManager->commit();
	}
}
