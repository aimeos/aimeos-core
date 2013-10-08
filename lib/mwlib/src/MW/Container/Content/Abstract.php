<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage Container
 */


/**
 * Common abstract class for content objects.
 *
 * @package MW
 * @subpackage Container
 */
abstract class MW_Container_Content_Abstract
{
	private $_resource;
	private $_options;
	private $_name;


	/**
	 * Initializes the CSV content object.
	 *
	 * @param resource|string $resource File pointer or path to the actual file
	 * @param string $name Name of the CSV file
	 * @param array $options Associative list of key/value pairs for configuration
	 */
	public function __construct( $resource, $name, array $options )
	{
		$this->_resource = $resource;
		$this->_options = $options;
		$this->_name = $name;
	}


	/**
	 * Returns the path of the actual file.
	 *
	 * @return string Path to the actual file
	 */
	public function getResource()
	{
		return $this->_resource;
	}


	/**
	 * Returns the name of the content object.
	 *
	 * @return string Name of the content object
	 */
	public function getName()
	{
		return $this->_name;
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
