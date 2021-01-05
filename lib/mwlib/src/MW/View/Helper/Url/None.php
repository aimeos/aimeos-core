<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
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
class None
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Url\Iface
{
	/**
	 * Returns an empty string as URL.
	 *
	 * @param string|null $target Route or page which should be the target of the link (if any)
	 * @param string|null $controller Name of the controller which should be part of the link (if any)
	 * @param string|null $action Name of the action which should be part of the link (if any)
	 * @param array $params Associative list of parameters that should be part of the URL
	 * @param string[] $trailing Trailing URL parts that are not relevant to identify the resource (for pretty URLs)
	 * @param array $config Additional configuration parameter per URL
	 * @return string Complete URL that can be used in the template
	 */
	public function transform( string$target = null, string$controller = null, string$action = null,
		array $params = [], array $trailing = [], array $config = [] ) : string
	{
		return '';
	}
}
