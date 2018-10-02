<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds default attribute
 */
class MShopAddAttributeData extends MShopAddDataAbstract
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddTypeData' );
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
	 * Adds the default codes
	 */
	protected function process()
	{
		\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\MShop\\Context\\Item\\Iface', $this->additional );

		$site = $this->additional->getLocale()->getSite()->getCode();
		$this->msg( sprintf( 'Adding default attribute data for site "%1$s"', $site ), 0 );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'default' . $ds . 'data' . $ds . 'attribute.php';

		if( ( $data = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for default codes', $path ) );
		}

		$manager = \Aimeos\MShop\Factory::createManager( $this->additional, 'attribute' );
		$item = $manager->createItem();
		$num = $total = 0;

		foreach( $data as $dataset )
		{
			try
			{
				$total++;
				$item->setId( null )->fromArray( $dataset );
				$item->setTypeId( $this->getTypeId( 'attribute/type', $dataset['attribute.domain'], $dataset['attribute.type'] ) );

				$manager->saveItem( $item );
				$num++;
			}
			catch( \Exception $e ) { ; } // if attribute was already available
		}

		$this->status( $num > 0 ? $num . '/' . $total : 'OK' );
	}
}