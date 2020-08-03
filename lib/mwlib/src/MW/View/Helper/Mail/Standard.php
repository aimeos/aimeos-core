<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Mail;


/**
 * View helper class for creating e-mails.
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Mail\Iface
{
	private $message;


	/**
	 * Initializes the Mail view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param \Aimeos\MW\Mail\Message\Iface $message E-mail message object
	 */
	public function __construct( \Aimeos\MW\View\Iface $view, \Aimeos\MW\Mail\Message\Iface $message )
	{
		parent::__construct( $view );

		$this->message = $message;
	}


	/**
	 * Returns the e-mail message object.
	 *
	 * @return \Aimeos\MW\Mail\Message\Iface E-mail message object
	 */
	public function transform() : \Aimeos\MW\Mail\Message\Iface
	{
		return $this->message;
	}
}
