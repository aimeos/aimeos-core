<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds default codes to tables
 */
class MShopAddCodeData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopAddLocaleData'];
	}


	public function up()
	{
	}


	/**
	 * Adds the default codes
	 */
	protected function process( array $data )
	{
		foreach( $data as $domain => $datasets )
		{
			$this->info( sprintf( 'Checking "%1$s" codes', $domain ), 'vv', 1 );

			$domainManager = \Aimeos\MShop::create( $this->context(), $domain );

			foreach( $datasets as $dataset )
			{
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
				}

				$domainManager->save( $item );
			}
		}
	}
}
