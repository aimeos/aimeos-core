<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds demo records to service tables.
 */
class DemoAddServiceData extends \Aimeos\MW\Setup\Task\MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['MShopAddTypeDataDefault'];
	}


	/**
	 * Insert service data.
	 */
	public function migrate()
	{
		$this->msg( 'Processing service demo data', 0 );

		$context = $this->getContext();
		$value = $context->getConfig()->get( 'setup/default/demo', '' );

		if( $value === '' )
		{
			$this->status( 'OK' );
			return;
		}


		$manager = \Aimeos\MShop::create( $context, 'service' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '=~', 'service.code', 'demo-' ) );
		$services = $manager->search( $search );

		foreach( $services as $item )
		{
			$this->removeItems( $item->getId(), 'service/lists', 'service', 'media' );
			$this->removeItems( $item->getId(), 'service/lists', 'service', 'price' );
			$this->removeItems( $item->getId(), 'service/lists', 'service', 'text' );
		}

		$manager->delete( $services->toArray() );


		if( $value === '1' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$path = __DIR__ . $ds . 'data' . $ds . 'demo-service.php';

			if( ( $data = include( $path ) ) == false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for service domain', $path ) );
			}

			foreach( $data as $entry )
			{
				$item = $manager->create();
				$item->setType( $entry['type'] );
				$item->setCode( $entry['code'] );
				$item->setLabel( $entry['label'] );
				$item->setProvider( $entry['provider'] );
				$item->setPosition( $entry['position'] );
				$item->setConfig( $entry['config'] );
				$item->setStatus( $entry['status'] );

				$manager->save( $item );

				if( isset( $entry['media'] ) ) {
					$this->addMedia( $item->getId(), $entry['media'], 'service' );
				}

				if( isset( $entry['price'] ) ) {
					$this->addPrices( $item->getId(), $entry['price'], 'service' );
				}

				if( isset( $entry['text'] ) ) {
					$this->addTexts( $item->getId(), $entry['text'], 'service' );
				}
			}

			$this->status( 'added' );
		}
		else
		{
			$this->status( 'removed' );
		}
	}
}
