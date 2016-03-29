<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
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
	private $objects = array();


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
	 * Returns the message queue for the given name
	 *
	 * @param string $name Key for the message queue
	 * @return \Aimeos\MW\MQueue\Iface Message queue object
	 * @throws \Aimeos\MW\MQueue\Exception If an no configuration for that name is found
	 */
	public function get( $name )
	{
		$conf = (array) $this->getConfig( $name );

		if( !isset( $this->objects[$name] ) ) {
			$this->objects[$name] = \Aimeos\MW\MQueue\Factory::create( $conf );
		}

		return $this->objects[$name];
	}


	/**
	 * Returns the configuration for the given name
	 *
	 * @param string &$name Name of the resource, e.g. "mq" or "mq-email"
	 * @return array|string Configuration values
	 * @throws \Aimeos\MW\MQueue\Exception If an no configuration for that name is found
	 */
	protected function getConfig( &$name )
	{
		if( ( $conf = $this->config->get( 'resource/' . $name ) ) !== null ) {
			return $conf;
		}

		$name = 'mq';
		if( ( $conf = $this->config->get( 'resource/mq' ) ) !== null ) {
			return $conf;
		}

		$msg = sprintf( 'No resource configuration for "%1$s" available', $name );
		throw new \Aimeos\MW\MQueue\Exception( $msg );
	}
}
