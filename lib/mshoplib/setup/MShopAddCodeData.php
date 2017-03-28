<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds default codes to tables
 */
class MShopAddCodeData extends \Aimeos\MW\Setup\Task\Base
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddLocaleData' );
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
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		// executed by tasks in sub-directories for specific sites
	}


	/**
	 * Adds the default codes
	 */
	protected function process()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$site = $this->additional->getLocale()->getSite()->getCode();
		$this->msg( sprintf( 'Adding default code data for site "%1$s"', $site ), 0 ); $this->status( '' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'default' . $ds . 'data' . $ds . 'code.php';

		if( ( $data = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for default codes', $path ) );
		}

		foreach( $data as $domain => $datasets )
		{
			$this->msg( sprintf( 'Checking "%1$s" codes', $domain ), 1 );

			$domainManager = \Aimeos\MShop\Factory::createManager( $this->additional, $domain );
			$type = $domainManager->createItem();
			$num = $total = 0;

			foreach( $datasets as $dataset )
			{
				$total++;

				$type->setId( null );
				$type->setCode( $dataset['code'] );
				$type->setLabel( $dataset['label'] );

				if( isset( $dataset['status'] ) ) {
					$type->setStatus( $dataset['status'] );
				}

				try {
					$domainManager->saveItem( $type );
					$num++;
				} catch( \Exception $e ) {; } // if type was already available
			}

			$this->status( $num > 0 ? $num . '/' . $total : 'OK' );
		}
	}
}