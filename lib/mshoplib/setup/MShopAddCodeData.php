<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	public function migrate()
	{
		// executed by tasks in sub-directories for specific sites
	}


	/**
	 * Adds the default codes
	 */
	protected function process( array $data )
	{
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