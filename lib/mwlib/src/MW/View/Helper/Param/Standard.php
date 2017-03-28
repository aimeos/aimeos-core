<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Param;


/**
 * View helper class for retrieving parameter values.
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Param\Iface
{
	private $params;


	/**
	 * Initializes the parameter view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param array $params Associative list of key/value pairs
	 */
	public function __construct( $view, array $params = [] )
	{
		parent::__construct( $view );

		$this->params = $params;
	}


	/**
	 * Returns the parameter value.
	 *
	 * @param string|null $name Name of the parameter key or null for all parameters
	 * @param mixed $default Default value if parameter key is not available
	 * @return mixed Parameter value or associative list of key/value pairs
	 */
	public function transform( $name = null, $default = null )
	{
		if( $name === null ) {
			return $this->params;
		}

		$parts = explode( '/', trim( $name, '/' ) );
		$param = $this->params;

		foreach( $parts as $part )
		{
			if( isset( $param[$part] ) ) {
				$param = $param[$part];
			} else {
				return $default;
			}
		}

		return $param;
	}
}