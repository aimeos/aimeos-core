<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs;


/**
 * Common methods for Jobs controller classes.
 *
 * @package Controller
 * @subpackage Jobs
 */
abstract class Base
	implements \Aimeos\MW\Macro\Iface
{
	use \Aimeos\MW\Macro\Traits;


	private $aimeos;
	private $context;


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param \Aimeos\Bootstrap $aimeos \Aimeos\Bootstrap main object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos )
	{
		$this->context = $context;
		$this->aimeos = $aimeos;
	}


	/**
	 * Catch unknown methods
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @throws \Aimeos\Controller\Jobs\Exception If method call failed
	 */
	public function __call( string $name, array $param )
	{
		throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Unable to call method "%1$s"', $name ) );
	}


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext() : \Aimeos\MShop\Context\Item\Iface
	{
		return $this->context;
	}


	/**
	 * Returns the \Aimeos\Bootstrap object.
	 *
	 * @return \Aimeos\Bootstrap \Aimeos\Bootstrap object
	 */
	protected function getAimeos() : \Aimeos\Bootstrap
	{
		return $this->aimeos;
	}


	/**
	 * Returns the value from the list or the default value
	 *
	 * @param array $list Associative list of key/value pairs
	 * @param string $key Key for the value to retrieve
	 * @param mixed $default Default value if key isn't found
	 * @return mixed Value for the key in the list or the default value
	 */
	protected function getValue( array $list, string $key, $default = null )
	{
		return isset( $list[$key] ) && ( $value = trim( $list[$key] ) ) !== '' ? $value : $default;
	}


	/**
	 * Sends a mail with the given data to the configured e-mails
	 *
	 * @param string $subject Subject of the e-mail
	 * @param string $body Text body of the e-mail
	 * @return \Aimeos\Controller\Jobs\Iface Same object for fluent interface
	 */
	protected function mail( string $subject, string $body ) : self
	{
		$config = $this->context->getConfig();

		/** resource/email/from-name
		 * Name of the e-mail sender
		 *
		 * Should be the company or web site name
		 *
		 * @param string Sender name
		 * @see resource/email/from-email
		 */
		$name = $config->get( 'resource/email/from-name' );

		/** resource/email/from-email
		 * E-Mail address of the sender
		 *
		 * Should be the e-mail address of the company or web site
		 *
		 * @param string E-Mail address
		 * @see resource/email/from-name
		 */
		$email = $config->get( 'resource/email/from-email' );

		/** controller/jobs/to-email
		 * Recipient e-mail address used when sending job e-mails
		 *
		 * Job controllers can send e-mails when they has finished of if an
		 * error occurred. This setting will be used as the recipient e-mail
		 * address for these e-mails.
		 *
		 * @param string E-mail address
		 * @since 2020.04
		 * @category User
		 * @see controller/jobs/from-email
		 */
		if( ( $to = $config->get( 'controller/jobs/to-email', $email ) ) == null ) {
			return $this;
		}

		$message = $this->context->getMail()->createMessage();

		foreach( (array) $to as $addr ) {
			$message->addTo( $addr, $name );
		}

		/** controller/jobs/from-email
		 * Sender e-mail address used when sending job e-mails
		 *
		 * Job controllers can send e-mails when they has finished of if an
		 * error occurred. This setting will be used as the sender e-mail
		 * address in these e-mails.
		 *
		 * @param string E-mail address
		 * @since 2020.04
		 * @category User
		 * @see controller/jobs/to-email
		 */
		if( $from = $config->get( 'controller/jobs/from-email', $email ) ) {
			$message->addFrom( $from, $name );
		}

		$message->setSubject( $subject )->setBody( $body )->send();

		return $this;
	}
}
