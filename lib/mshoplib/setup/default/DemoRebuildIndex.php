<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Rebuilds the index.
 */
class DemoRebuildIndex extends \Aimeos\MW\Setup\Task\Base
{
	public function __construct( \Aimeos\MW\Setup\DBSchema\Iface $schema, \Aimeos\MW\DB\Connection\Iface $conn, $additional = null )
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $additional );

		parent::__construct( $schema, $conn, $additional );
	}


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['MShopSetLocale'];
	}


	/**
	 * Rebuilds the index.
	 */
	public function migrate()
	{
		$this->msg( 'Rebuilding index for demo data', 0 );

		if( $this->additional->getConfig()->get( 'setup/default/demo', '' ) === '' )
		{
			$this->status( 'OK' );
			return;
		}

		$timestamp = date( 'Y-m-d H:i:s' );
		\Aimeos\MShop::create( $this->additional, 'index' )->rebuild()->cleanup( $timestamp );

		$this->status( 'done' );
	}
}
