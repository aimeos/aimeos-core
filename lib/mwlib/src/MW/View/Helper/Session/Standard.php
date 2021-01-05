<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Session;


/**
 * View helper class for retrieving session values.
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Session\Iface
{
	private $session;


	/**
	 * Initializes the session view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param \Aimeos\MW\Session\Iface $session Session object
	 */
	public function __construct( \Aimeos\MW\View\Iface $view, \Aimeos\MW\Session\Iface $session )
	{
		parent::__construct( $view );

		$this->session = $session;
	}


	/**
	 * Returns the session value.
	 *
	 * @param string $name Name of the session key
	 * @param mixed $default Default value if session key is not available
	 * @return mixed Session value
	 */
	public function transform( string $name, $default = null )
	{
		return $this->session->get( $name, $default );
	}
}
