<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Config;


/**
 * View helper class for retrieving configuration values.
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Config\Iface
{
	private $config;


	/**
	 * Initializes the config view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param \Aimeos\MW\Config\Iface $config Configuration object
	 */
	public function __construct( $view, \Aimeos\MW\Config\Iface $config )
	{
		parent::__construct( $view );

		$this->config = $config;
	}


	/**
	 * Returns the config value.
	 *
	 * @param string|null $name Name of the config key or null for all parameters
	 * @param mixed $default Default value if config key is not available
	 * @return mixed Config value or associative list of key/value pairs
	 */
	public function transform( $name = null, $default = null )
	{
		return $this->config->get( $name, $default );
	}
}