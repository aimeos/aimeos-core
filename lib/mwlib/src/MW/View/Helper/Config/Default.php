<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for retrieving configuration values.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Config_Default
	extends MW_View_Helper_Base
	implements MW_View_Helper_Interface
{
	private $config;


	/**
	 * Initializes the config view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param MW_Config_Interface $config Configuration object
	 */
	public function __construct( $view, MW_Config_Interface $config )
	{
		parent::__construct( $view );

		$this->config = $config;
	}


	/**
	 * Returns the config value.
	 *
	 * @param string $name Name of the config key or null for all parameters
	 * @param string $default Default value if config key is not available
	 * @return mixed Config value or associative list of key/value pairs
	 */
	public function transform( $name = null, $default = null )
	{
		return $this->config->get( $name, $default );
	}
}