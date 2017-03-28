<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds media test data.
 */
class MediaListAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'AttributeListAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Adds media test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding media-list test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'media-list.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for media list domain', $path ) );
		}

		$refKeys = [];
		foreach( $testdata['media/lists'] as $dataset ) {
			$refKeys[$dataset['domain']][] = $dataset['refid'];
		}

		$refIds = [];
		$refIds['text'] = $this->getTextData( $refKeys['text'] );
		$refIds['attribute'] = $this->getAttributeData( $refKeys['attribute'] );

		$this->addMediaListData( $testdata, $refIds );

		$this->status( 'done' );
	}


	/**
	 * Gets required attribute item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function getAttributeData( array $keys )
	{
		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $this->additional, 'Standard' );
		$attributeTypeManager = $attributeManager->getSubManager( 'type', 'Standard' );

		$codes = $typeCodes = $domains = [];
		foreach( $keys as $dataset )
		{
			$exp = explode( '/', $dataset );

			if( count( $exp ) != 4 ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref attribute are set wrong "%1$s"', $dataset ) );
			}

			$domains[] = $exp[1];
			$typeCodes[] = $exp[2];
			$codes[] = $exp[3];
		}

		$search = $attributeTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.type.domain', $domains ),
			$search->compare( '==', 'attribute.type.code', $typeCodes ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $attributeTypeManager->searchItems( $search );

		$typeids = [];
		foreach( $result as $item ) {
			$typeids[] = $item->getId();
		}

		$search = $attributeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', $codes ),
			$search->compare( '==', 'attribute.typeid', $typeids ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$refIds = [];
		foreach( $attributeManager->searchItems( $search ) as $item ) {
			$refIds['attribute/' . $item->getDomain() . '/' . $item->getType() . '/' . $item->getCode()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Gets required text item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function getTextData( array $keys )
	{
		$textManager = \Aimeos\MShop\Text\Manager\Factory::createManager( $this->additional, 'Standard' );

		$labels = [];
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref text are set wrong "%1$s"', $dataset ) );
			}

			$labels[] = $str;
		}

		$search = $textManager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.label', $labels ) );

		$refIds = [];
		foreach( $textManager->searchItems( $search ) as $item ) {
			$refIds['text/' . $item->getLabel()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Adds the media-list test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $refIds Associative list of domains and the keys/IDs of the inserted items
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addMediaListData( array $testdata, array $refIds )
	{
		$mediaManager = \Aimeos\MShop\Media\Manager\Factory::createManager( $this->additional, 'Standard' );
		$mediaListManager = $mediaManager->getSubmanager( 'lists', 'Standard' );
		$mediaListTypeManager = $mediaListManager->getSubManager( 'type', 'Standard' );

		$urls = [];
		foreach( $testdata['media/lists'] as $dataset )
		{
			if( ( $pos = strpos( $dataset['parentid'], '/' ) ) === false || ( $str = substr( $dataset['parentid'], $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
			}

			$urls[] = $str;
		}

		$search = $mediaManager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.url', $urls ) );

		$result = $mediaManager->searchItems( $search );

		$parentIds = [];
		foreach( $result as $item ) {
			$parentIds['media/' . $item->getUrl()] = $item->getId();
		}

		$medListTypes = [];
		$medListType = $mediaListTypeManager->createItem();

		foreach( $testdata['media/lists/type'] as $key => $dataset )
		{
			$medListType->setId( null );
			$medListType->setCode( $dataset['code'] );
			$medListType->setDomain( $dataset['domain'] );
			$medListType->setLabel( $dataset['label'] );
			$medListType->setStatus( $dataset['status'] );

			$mediaListTypeManager->saveItem( $medListType );
			$medListTypes[$key] = $medListType->getId();
		}

		$this->conn->begin();

		$medList = $mediaListManager->createItem();
		foreach( $testdata['media/lists'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['parentid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No media ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $medListTypes[$dataset['typeid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No media list type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			if( !isset( $refIds[$dataset['domain']][$dataset['refid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No "%1$s" ref ID found for "%2$s"', $dataset['refid'], $dataset['domain'] ) );
			}

			$medList->setId( null );
			$medList->setParentId( $parentIds[$dataset['parentid']] );
			$medList->setTypeId( $medListTypes[$dataset['typeid']] );
			$medList->setRefId( $refIds[$dataset['domain']] [$dataset['refid']] );
			$medList->setDomain( $dataset['domain'] );
			$medList->setDateStart( $dataset['start'] );
			$medList->setDateEnd( $dataset['end'] );
			$medList->setConfig( $dataset['config'] );
			$medList->setPosition( $dataset['pos'] );
			$medList->setStatus( $dataset['status'] );

			$mediaListManager->saveItem( $medList, false );
		}

		$this->conn->commit();
	}
}