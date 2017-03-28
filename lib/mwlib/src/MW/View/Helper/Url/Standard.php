<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Url;


/**
 * View helper class for building URLs.
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Url\Base
	implements \Aimeos\MW\View\Helper\Url\Iface
{
	private $baseUrl;


	/**
	 * Initializes the URL view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param string $baseUrl URL which acts as base for all constructed URLs
	 */
	public function __construct( $view, $baseUrl )
	{
		parent::__construct( $view );

		$this->baseUrl = rtrim( $baseUrl, '/' );
	}


	/**
	 * Returns the URL assembled from the given arguments.
	 *
	 * @param string|null $target Route or page which should be the target of the link (if any)
	 * @param string|null $controller Name of the controller which should be part of the link (if any)
	 * @param string|null $action Name of the action which should be part of the link (if any)
	 * @param array $params Associative list of parameters that should be part of the URL
	 * @param string[] $trailing Trailing URL parts that are not relevant to identify the resource (for pretty URLs)
	 * @param array $config Additional configuration parameter per URL
	 * @return string Complete URL that can be used in the template
	 */
	public function transform( $target = null, $controller = null, $action = null, array $params = [], array $trailing = [], array $config = [] )
	{
		$path = ( $target !== null ? $target . '/' : '' );
		$path .= ( $controller !== null ? $controller . '/' : '' );
		$path .= ( $action !== null ? $action . '/' : '' );

		$parameter = ( count( $params ) > 0 ? '?' . http_build_query( $this->sanitize( $params ) ) : '' );
		$pretty = ( count( $trailing ) > 0 ? implode( '-', $this->sanitize( $trailing ) ) : '' );

		return $this->baseUrl . '/' . $path . $pretty . $parameter;
	}
}