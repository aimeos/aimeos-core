<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds demo records to catalog tables.
 */
class DemoAddCatalogData extends \Aimeos\MW\Setup\Task\MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'DemoAddProductData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'DemoRebuildIndex' );
	}


	/**
	 * Insert catalog nodes and relations.
	 */
	public function migrate()
	{
		$this->msg( 'Processing catalog demo data', 0 );

		$context = $this->getContext();
		$value = $context->getConfig()->get( 'setup/default/demo', '' );

		if( $value === '' )
		{
			$this->status( 'OK' );
			return;
		}


		$item = null;
		$manager = \Aimeos\MShop\Factory::createManager( $context, 'catalog' );

		try
		{
			// Don't delete the catalog node because users are likely use it for production
			$item = $manager->getTree( null, [], \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );

			$this->removeItems( $item->getId(), 'catalog/lists', 'catalog', 'media' );
			$this->removeItems( $item->getId(), 'catalog/lists', 'catalog', 'text' );
			$this->removeListItems( $item->getId(), 'catalog/lists', 'product' );
		}
		catch( \Exception $e ) {; } // If no root node was already inserted into the database


		if( $value === '1' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$path = __DIR__ . $ds . 'data' . $ds . 'demo-catalog.php';

			if( ( $data = include( $path ) ) == false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for catalog domain', $path ) );
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
				$this->addMedia( $item->getId(), $data['media'], 'catalog' );
			}

			if( isset( $data['product'] ) ) {
				$this->addProducts( $item->getId(), $data['product'], 'catalog' );
			}

			if( isset( $data['text'] ) ) {
				$this->addTexts( $item->getId(), $data['text'], 'catalog' );
			}

			$this->status( 'added' );
		}
		else
		{
			$this->status( 'removed' );
		}
	}
}