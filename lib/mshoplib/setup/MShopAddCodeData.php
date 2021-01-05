<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	public function getPreDependencies() : array
	{
		return ['MShopAddLocaleData'];
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

			$domainManager = \Aimeos\MShop::create( $this->additional, $domain );
			$num = $total = 0;

			foreach( $datasets as $dataset )
			{
				$total++;

				try
				{
					$item = $domainManager->find( $dataset['code'] );
				}
				catch( \Exception $e )
				{
					$item = $domainManager->create();
					$item->setCode( $dataset['code'] );
					$item->setLabel( $dataset['label'] );

					if( isset( $dataset['status'] ) ) {
						$item->setStatus( $dataset['status'] );
					}

					$num++;
				}

				$domainManager->save( $item );
			}

			$this->status( $num > 0 ? $num . '/' . $total : 'OK' );
		}
	}
}
