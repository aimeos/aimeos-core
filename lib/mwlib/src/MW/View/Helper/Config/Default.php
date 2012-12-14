<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 * @version $Id$
 */


/**
 * View helper class for retrieving configuration values.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Config_Default
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	private $_config;


	/**
	 * Initializes the config view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param array $config Associative list of key/value pairs
	 */
	public function __construct( $view, array $config )
	{
		parent::__construct( $view );

		$this->_config = $config;
	}


	/**
	 * Returns the config value.
	 *
	 * @param string $name Name of the config key or null for all parameters
	 * @param mixed $default Default value if config key is not available
	 * @return mixed Config value or associative list of key/value pairs
	 */
	public function transform( $name = null, $default = null )
	{
		if( $name === null ) {
			return $this->_params;
		}

		if( isset( $this->_config[$name] ) ) {
			return $this->_config[$name];
		}

		return $default;
	}
}