<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds demo records to catalog tables.
 */
class MW_Setup_Task_DemoAddCatalogData extends MW_Setup_Task_MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'DemoAddProductData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'DemoRebuildIndex' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Insert catalog nodes and relations.
	 */
	protected function _process()
	{
		$this->_msg( 'Processing catalog demo data', 0 );

		$item = null;
		$context =  $this->_getContext();
		$manager = MShop_Factory::createManager( $context, 'catalog' );

		try
		{
			// Don't delete the catalog node because users are likely use it for production
			$item = $manager->getTree( null, array(), MW_Tree_Manager_Abstract::LEVEL_ONE );

			$this->_removeItems( $item->getId(), 'catalog/list', 'catalog', 'media' );
			$this->_removeItems( $item->getId(), 'catalog/list', 'catalog', 'text' );
			$this->_removeListItems( $item->getId(), 'catalog/list', 'product' );
		}
		catch( Exception $e ) { ; }


		if( $context->getConfig()->get( 'setup/default/demo', false ) == true )
		{
			$ds = DIRECTORY_SEPARATOR;
			$path = __DIR__ . $ds . 'data' . $ds . 'demo-catalog.php';

			if( ( $data = include( $path ) ) == false ) {
				throw new MShop_Exception( sprintf( 'No file "%1$s" found for catalog domain', $path ) );
			}

			if( $item === null )
			{
				$item = $manager->createItem();
				$item->setCode( $data['code'] );
				$item->setLabel( $data['label'] );
				$item->setConfig( $data['config'] );
				$item->setStatus( $data['status'] );

				$manager->insertItem( $item );
			}


			if( isset( $data['media'] ) ) {
				$this->_addMedia( $item->getId(), $data['media'], 'catalog' );
			}

			if( isset( $data['product'] ) ) {
				$this->_addProducts( $item->getId(), $data['product'], 'catalog' );
			}

			if( isset( $data['text'] ) ) {
				$this->_addTexts( $item->getId(), $data['text'], 'catalog' );
			}

			$this->_status( 'added' );
		}
		else
		{
			$this->_status( 'removed' );
		}
	}
}