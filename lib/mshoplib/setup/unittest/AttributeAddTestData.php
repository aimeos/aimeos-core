<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds attribute test data and all items from other domains.
 */
class MW_Setup_Task_AttributeAddTestData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'TextAddTestData', 'MediaAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildTestIndex' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Adds attribute test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding attribute test data', 0 );
		$this->_additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'attribute.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for attribute domain', $path ) );
		}

		$this->_addAttributeData( $testdata );

		$this->_status( 'done' );
	}


	/**
	 * Adds the attribute test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function _addAttributeData( array $testdata )
	{
		$attributeManager = MShop_Attribute_Manager_Factory::createManager( $this->_additional, 'Default' );
		$attributeTypeManager = $attributeManager->getSubManager( 'type', 'Default' );

		$atypeIds = array();
		$atype = $attributeTypeManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['attribute/type'] as $key => $dataset )
		{
			$atype->setId( null );
			$atype->setCode( $dataset['code'] );
			$atype->setDomain( $dataset['domain'] );
			$atype->setLabel( $dataset['label'] );
			$atype->setStatus( $dataset['status'] );

			$attributeTypeManager->saveItem( $atype );
			$atypeIds[$key] = $atype->getId();
		}

		$attribute = $attributeManager->createItem();
		foreach( $testdata['attribute'] as $key => $dataset )
		{
			if( !isset( $atypeIds[$dataset['typeid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No attribute type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$attribute->setId( null );
			$attribute->setDomain( $dataset['domain'] );
			$attribute->setTypeId( $atypeIds[$dataset['typeid']] );
			$attribute->setCode( $dataset['code'] );
			$attribute->setLabel( $dataset['label'] );
			$attribute->setStatus( $dataset['status'] );
			$attribute->setPosition( $dataset['pos'] );

			$attributeManager->saveItem( $attribute, false );
		}

		$this->_conn->commit();
	}
}