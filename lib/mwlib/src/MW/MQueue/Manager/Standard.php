<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 * @package MW
 * @subpackage MQueue
 */


namespace Aimeos\MW\MQueue\Manager;


/**
 * Standard message queue manager
 *
 * @package MW
 * @subpackage MQueue
 */
class Standard implements Iface
{
	private $config;
	private $objects = [];


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MW\Config\Iface $config Configuration object
	 */
	public function __construct( \Aimeos\MW\Config\Iface $config )
	{
		$this->config = $config;
	}


	/**
	 * Cleans up the object
	 */
	public function __destruct()
	{
		foreach( $this->objects as $object ) {
			unset( $object );
		}
	}


	/**
	 * Clones the objects inside.
	 */
	public function __clone()
	{
		$this->config = clone $this->config;

		foreach( $this->objects as $resource => $object ) {
			unset( $this->objects[$resource] );
		}
	}


	/**
	 * Returns the message queue for the given name
	 *
	 * @param string $resource Resource name of the message queue
	 * @return \Aimeos\MW\MQueue\Iface Message queue object
	 * @throws \Aimeos\MW\MQueue\Exception If an no configuration for that name is found
	 */
	public function get( string $resource ) : \Aimeos\MW\MQueue\Iface
	{
		$conf = (array) $this->getConfig( $resource );

		if( isset( $conf['db'] ) && is_string( $conf['db'] ) ) {
			$conf['db'] = (array) $this->getConfig( $conf['db'] );
		}

		if( !isset( $this->objects[$resource] ) ) {
			$this->objects[$resource] = \Aimeos\MW\MQueue\Factory::create( $conf );
		}

		return $this->objects[$resource];
	}


	/**
	 * Returns the configuration for the given name
	 *
	 * @param string &$resource Name of the resource, e.g. "mq" or "mq-email"
	 * @return array Configuration values
	 * @throws \Aimeos\MW\MQueue\Exception If an no configuration for that name is found
	 */
	protected function getConfig( string &$resource ) : array
	{
		if( ( $conf = $this->config->get( 'resource/' . $resource ) ) !== null ) {
			return $conf;
		}

		$resource = 'mq';
		if( ( $conf = $this->config->get( 'resource/mq' ) ) !== null ) {
			return $conf;
		}

		$msg = sprintf( 'No resource configuration for "%1$s" available', $resource );
		throw new \Aimeos\MW\MQueue\Exception( $msg );
	}
}
