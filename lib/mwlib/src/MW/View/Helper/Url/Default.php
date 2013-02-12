<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 * @version $Id$
 */


/**
 * View helper class for building URLs.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Url_Default
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	private $_baseUrl;


	/**
	 * Initializes the URL view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param string $baseUrl URL which acts as base for all constructed URLs
	 */
	public function __construct( $view, $baseUrl )
	{
		parent::__construct( $view );

		$this->_baseUrl = rtrim( $baseUrl, '/' );
	}


	/**
	 * Returns the URL assembled from the given arguments.
	 *
	 * @param string|null $target Route or page which should be the target of the link (if any)
	 * @param string|null $controller Name of the controller which should be part of the link (if any)
	 * @param string|null $action Name of the action which should be part of the link (if any)
	 * @param array $params Associative list of parameters that should be part of the URL
	 * @param array $trailing Trailing URL parts that are not relevant to identify the resource (for pretty URLs)
	 * @return string Complete URL that can be used in the template
	 */
	public function transform( $target = null, $controller = null, $action = null, array $params = array(), array $trailing = array() )
	{
		$path = ( $target !== null ? $target . '/' : '' );
		$path .= ( $controller !== null ? $controller . '/' : '' );
		$path .= ( $action !== null ? $action . '/' : '' );

		$parameter = ( count( $params ) > 0 ? '?' . http_build_query( $params ) : '' );
		$pretty = ( count( $trailing ) > 0 ? implode( '/', $trailing ) : '' );

		$badchars = array( ' ', '&', '%', '?', '#', '=', '{', '}', '|', '\\', '^', '~', '[', ']', '`' );
		$pretty = str_replace( $badchars, '-', $pretty );
		
		return $this->_baseUrl . '/' . $path . $pretty . $parameter;
	}
}