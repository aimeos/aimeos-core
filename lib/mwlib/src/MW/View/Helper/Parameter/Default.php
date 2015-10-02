<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for retrieving parameter values.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Parameter_Default
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	private $params;


	/**
	 * Initializes the parameter view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param array $params Associative list of key/value pairs
	 */
	public function __construct( $view, array $params )
	{
		parent::__construct( $view );

		$this->params = $params;
	}


	/**
	 * Returns the parameter value.
	 *
	 * @param string|null $name Name of the parameter key or null for all parameters
	 * @param string $default Default value if parameter key is not available
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