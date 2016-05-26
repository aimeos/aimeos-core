<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage MQueue
 */


namespace Aimeos\MW\MQueue;


/**
 * Base class for all message queue implementations
 *
 * @package MW
 * @subpackage MQueue
 */
abstract class Base
{
	private $config;


	/**
	 * Initializes the object
	 *
	 * @param array $config Multi-dimensional associative list of configuration settings
	 */
	public function __construct( array $config )
	{
		$this->config = $config;
	}


	/**
	 * Returns the configuration setting for the given key
	 *
	 * @param string $key Configuration key like "host" or "db/host"
	 * @param mixed $default Default value if no setting is found
	 * @return mixed Configuration setting or default value
	 */
	protected function getConfig( $key, $default = null )
	{
		$config = $this->config;

		foreach( explode( '/', trim( $key, '/' ) ) as $part )
		{
			if( isset( $config[$part] ) ) {
				$config = $config[$part];
			} else {
				return $default;
			}
		}

		return $config;
	}
}