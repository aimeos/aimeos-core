<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage MQueue
 */


namespace Aimeos\MW\MQueue;


/**
 * Creates a new message queue object
 *
 * @package MW
 * @subpackage MQueue
 */
class Factory
{
	/**
	 * Creates and returns a new message queue object
	 *
	 * @param array $config Resource configuration
	 * @return \Aimeos\MW\MQueue\Iface Message queue object
	 * @throws \Aimeos\MW\MQueue\Exception if message queue class isn't found
	 */
	static public function create( array $config )
	{
		if( !isset( $config['adapter'] ) ) {
			throw new \Aimeos\MW\MQueue\Exception( 'Message queue not configured' );
		}

		$classname = '\\Aimeos\\MW\\MQueue\\' . ucfirst( (string) $config['adapter'] );

		if( !class_exists( $classname ) ) {
			throw new \Aimeos\MW\MQueue\Exception( sprintf( 'Message queue "%1$s" not found', $config['adapter'] ) );
		}

		return new $classname( $config );
	}
}
