<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds default attribute
 */
class MShopAddAttributeData extends MShopAddDataAbstract
{
	public function after() : array
	{
		return ['MShopAddTypeData'];
	}


	public function up()
	{
	}


	/**
	 * Adds the default codes
	 */
	protected function process()
	{
		$site = $this->context()->getLocale()->getSiteItem()->getCode();
		$this->info( sprintf( 'Adding default attribute data for site "%1$s"', $site ), 'v' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'default' . $ds . 'data' . $ds . 'attribute.php';

		if( ( $data = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for default codes', $path ) );
		}

		$manager = \Aimeos\MShop::create( $this->context(), 'attribute' );
		$item = $manager->create();

		foreach( $data as $dataset )
		{
			try
			{
				$item = $item->setId( null )->fromArray( $dataset );
				$manager->save( $item );
			}
			catch( \Exception $e ) { ; } // if attribute was already available
		}
	}
}
