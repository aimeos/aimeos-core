<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds demo records to catalog tables.
 */
class DemoAddCatalogData extends MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Catalog', 'Media', 'Product', 'Text', 'MShopSetLocale'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function before() : array
	{
		return ['DemoRebuildIndex'];
	}


	/**
	 * Insert catalog nodes and relations.
	 */
	public function up()
	{
		$this->info( 'Processing catalog demo data', 'vv' );

		$context = $this->context();

		if( ( $value = $context->config()->get( 'setup/default/demo', '' ) ) === '' ) {
			return;
		}


		$item = null;
		$manager = \Aimeos\MShop::create( $context, 'catalog' );

		try
		{
			// Don't delete the catalog node because users are likely use it for production
			$item = $manager->getTree( null, [], \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );

			$this->removeItems( $item->getId(), 'catalog/lists', 'catalog', 'media' );
			$this->removeItems( $item->getId(), 'catalog/lists', 'catalog', 'text' );
			$this->removeListItems( $item->getId(), 'catalog/lists', 'product' );
		}
		catch( \Exception $e ) { ; } // If no root node was already inserted into the database

		$search = $manager->filter();
		$search->add( $search->and( [
			$search->compare( '=~', 'catalog.code', 'demo-' ),
			$search->compare( '==', 'catalog.level', 1 )
		] ) );
		$manager->delete( $manager->search( $search )->getId()->toArray() );


		if( $value === '1' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$path = __DIR__ . $ds . 'data' . $ds . 'demo-catalog.php';

			if( ( $data = include( $path ) ) == false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for catalog domain', $path ) );
			}

			if( $item === null ) {
				$item = $manager->insert( $manager->create()->fromArray( $data ) );
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

			if( isset( $data['catalog'] ) ) {
				$this->addCatalog( $item->getId(), $data['catalog'], 'catalog' );
			}
		}
	}


	/**
	 * Adds the catalog items including referenced items
	 *
	 * @param string $id Unique ID of the parent category
	 * @param array $data List of category data
	 * @param string $domain Parent domain name (catalog)
	 */
	protected function addCatalog( string $id, array $data, string $domain )
	{
		$manager = \Aimeos\MShop::create( $this->context(), $domain );

		foreach( $data as $entry )
		{
			$item = $manager->create()->fromArray( $entry );
			$item = $manager->insert( $item, $id );

			if( isset( $entry['media'] ) ) {
				$this->addMedia( $item->getId(), $entry['media'], $domain );
			}

			if( isset( $entry['product'] ) ) {
				$this->addProducts( $item->getId(), $entry['product'], $domain );
			}

			if( isset( $entry['text'] ) ) {
				$this->addTexts( $item->getId(), $entry['text'], $domain );
			}

			if( isset( $entry['catalog'] ) ) {
				$this->addCatalog( $item->getId(), $entry['catalog'], $domain );
			}
		}
	}
}
