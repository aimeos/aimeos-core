<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2024
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
		$context = $this->context();

		if( ( $value = $context->config()->get( 'setup/default/demo', '' ) ) === '' ) {
			return;
		}


		$this->info( 'Processing catalog demo data', 'vv' );

		$item = $this->removeItems();


		if( $value === '1' ) {
			$this->addDemoData();
		}
	}


	/**
	 * Adds the demo data to the database.
	 *
	 * @throws \RuntimeException If the file isn't found
	 */
	protected function addDemoData()
	{
		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'demo-catalog.php';

		if( ( $data = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for catalog domain', $path ) );
		}

		$context = $this->context();
		$manager = \Aimeos\MShop::create( $context, 'catalog' );

		try {
			$item = $manager->getTree( null, ['media', 'text'], \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );
		} catch( \Exception $e ) {
			$item = $manager->insert( $manager->create()->fromArray( $data ) );
		}

		$manager->save( $this->addRefItems( $item, $data ) );
	}


	/**
	 * Adds the categories from the given entry data to the passed item.
	 *
	 * @param \Aimeos\MShop\Common\Item\ListsRef\Iface $item Item with list items
	 * @param array $entry Associative list of data with attribute, catalog, media, price, text or product sections
	 * @return \Aimeos\MShop\Common\Item\ListsRef\Iface $item Updated item
	 */
	protected function addCategories( \Aimeos\MShop\Common\Item\ListsRef\Iface $item, array $data, int $idx ) : \Aimeos\MShop\Common\Item\ListsRef\Iface
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'catalog' );

		foreach( $data as $idx => $entry )
		{
			$catItem = $manager->create()->fromArray( $entry );
			$catItem = $manager->insert( $catItem, $item->getId() );

			$manager->save( $this->addRefItems( $catItem, $entry, $idx ) );
		}

		return $item;
	}


	/**
	 * Deletes the demo catalog items
	 *
	 * @return \Aimeos\MShop\Catalog\Item\Iface|null Root catalog item whose reference data was removed
	 */
	protected function removeItems() : ?\Aimeos\MShop\Catalog\Item\Iface
	{
		$context = $this->context();
		$domains = ['media', 'text'];
		$manager = \Aimeos\MShop::create( $context, 'catalog' );

		try
		{
			// Don't delete the catalog node because users are likely use it for production
			$item = $manager->getTree( null, $domains, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );
			$this->removeRefItems( map( $item ), $domains );
			$manager->save( $item );
		}
		catch( \Exception $e ) { ; } // If no root node was already inserted into the database

		$filter = $manager->filter()
			->add( 'catalog.code', '=~', 'demo-' )
			->add( 'catalog.level', '==', 1 )
			->slice( 0, 0x7fffffff );
		$items = $manager->search( $filter, $domains );

		$this->removeRefItems( $items, $domains );

		$manager->delete( $items );

		return $item ?? null;
	}
}
