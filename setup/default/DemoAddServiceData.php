<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds demo records to service tables.
 */
class DemoAddServiceData extends MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Service', 'Media', 'Price', 'Text', 'MShopAddTypeDataDefault'];
	}


	/**
	 * Insert service data.
	 */
	public function up()
	{
		$context = $this->context();
		$value = $context->config()->get( 'setup/default/demo', '' );

		if( $value === '' ) {
			return;
		}


		$this->info( 'Processing service demo data', 'vv' );

		$this->removeItems();


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
		$path = __DIR__ . $ds . 'data' . $ds . 'demo-service.php';

		if( ( $data = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for service domain', $path ) );
		}

		$manager = \Aimeos\MShop::create( $this->context(), 'service' );

		foreach( $data as $idx => $entry )
		{
			$item = $manager->create()->fromArray( $entry );

			$this->addRefItems( $item, $entry, $idx );

			$manager->save( $item );
		}
	}


	/**
	 * Deletes the demo service items
	 *
	 * @return \Aimeos\Map List of items that were removed from the database
	 */
	protected function removeItems() : \Aimeos\Map
	{
		$context = $this->context();
		$domains = ['media', 'price', 'text'];
		$manager = \Aimeos\MShop::create( $context, 'service' );

		$filter = $manager->filter()->add( 'service.code', '=~', 'demo-' )->slice( 0, 0x7fffffff );
		$items = $manager->search( $filter, $domains );

		$this->removeRefItems( $items, $domains );
		$manager->delete( $items );

		return $items;
	}
}
