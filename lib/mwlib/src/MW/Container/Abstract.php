<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage Container
 */


/**
 * Common abstract class for container objects.
 *
 * @package MW
 * @subpackage Container
 */
abstract class MW_Container_Abstract
{
	private $_options;


	/**
	 * Initializes the object.
	 *
	 * @param array $options Associative list of key/value pairs for configuration
	 */
	public function __construct( array $options )
	{
		$this->_options = $options;
	}


	/**
	 * Returns the configured value for the given name or the default value if nothing is configured.
	 *
	 * @param string $name Name of the configuration option
	 * @param mixed $default Default value if option is not configured
	 * @return mixed Option value
	 */
	protected function _getOption( $name, $default = null )
	{
		return ( isset( $this->_options[$name] ) ? $this->_options[$name] : $default );
	}
}
