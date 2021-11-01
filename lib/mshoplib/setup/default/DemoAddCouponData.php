<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds demo records to coupon tables.
 */
class DemoAddCouponData extends MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopAddLocaleDataDefault'];
	}


	/**
	 * Insert service data.
	 */
	public function up()
	{
		$this->info( 'Processing coupon demo data', 'v' );

		$context = $this->context();
		$value = $context->getConfig()->get( 'setup/default/demo', '' );

		if( $value === '' ) {
			return;
		}


		$manager = \Aimeos\MShop::create( $context, 'coupon' );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '=~', 'coupon.label', 'demo-' ) );
		$services = $manager->search( $search );

		$manager->delete( $services->toArray() );


		if( $value === '1' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$path = __DIR__ . $ds . 'data' . $ds . 'demo-coupon.php';

			if( ( $data = include( $path ) ) == false ) {
				throw new \RuntimeException( sprintf( 'No file "%1$s" found for coupon domain', $path ) );
			}

			foreach( $data as $entry )
			{
				$item = $manager->create();
				$item->setLabel( $entry['label'] );
				$item->setProvider( $entry['provider'] );
				$item->setDateStart( $entry['datestart'] );
				$item->setDateEnd( $entry['dateend'] );
				$item->setConfig( $entry['config'] );
				$item->setStatus( $entry['status'] );

				$manager->save( $item );

				$this->addCodes( $item->getId(), $entry['codes'] );
			}
		}
	}


	/**
	 * Adds the coupon codes to the database.
	 *
	 * @param string $couponId
	 * @param array $data
	 */
	protected function addCodes( $couponId, array $data )
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'coupon/code' );

		foreach( $data as $entry )
		{
			$item = $manager->create();
			$item->setParentId( $couponId );
			$item->setCode( $entry['code'] );
			$item->setCount( $entry['count'] );
			$item->setDateStart( $entry['datestart'] );
			$item->setDateEnd( $entry['dateend'] );

			$manager->save( $item );
		}
	}
}
