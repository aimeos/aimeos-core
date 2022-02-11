<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs;


/**
 * Mailto trait for job controllers
 *
 * @package Controller
 * @subpackage Jobs
 */
trait Mailto
{
	/**
	 * Returns the context object
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	abstract function context() : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Prepares and returns a new mail message
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $addr Address item object
	 * @return \Aimeos\MW\Mail\Message\Iface Prepared mail message
	 */
	protected function mailTo( \Aimeos\MShop\Common\Item\Address\Iface $addr ) : \Aimeos\MW\Mail\Message\Iface
	{
		$context = $this->context();
		$config = $context->config();

		return $context->mail()
			->header( 'X-MailGenerator', 'Aimeos' )
			->from( $config->get( 'resource/email/from-email' ), $config->get( 'resource/email/from-name' ) )
			->to( $addr->getEMail(), $addr->getFirstName() . ' ' . $addr->getLastName() );
	}
}
