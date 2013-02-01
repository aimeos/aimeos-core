<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 * @version $Id$
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
	private $_params;


	/**
	 * Initializes the parameter view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param array $params Associative list of key/value pairs
	 */
	public function __construct( $view, array $params )
	{
		parent::__construct( $view );

		$this->_params = $params;
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
			return $this->_params;
		}

		$parts = explode( '/', trim( $name, '/' ) );
		$param = $this->_params;

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