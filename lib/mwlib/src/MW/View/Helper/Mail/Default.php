<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for creating e-mails.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Mail_Default
	extends MW_View_Helper_Base
	implements MW_View_Helper_Iface
{
	private $message;


	/**
	 * Initializes the Mail view helper.
	 *
	 * @param MW_View_Iface $view View instance with registered view helpers
	 * @param MW_Mail_Message_Iface $message E-mail message object
	 */
	public function __construct( $view, MW_Mail_Message_Iface $message )
	{
		parent::__construct( $view );

		$this->message = $message;
	}


	/**
	 * Returns the e-mail message object.
	 *
	 * @return MW_Mail_Message_Iface E-mail message object
	 */
	public function transform()
	{
		return $this->message;
	}
}
