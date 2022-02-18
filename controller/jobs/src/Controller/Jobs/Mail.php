<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs;


/**
 * Mail trait for job controllers
 *
 * @package Controller
 * @subpackage Jobs
 */
trait Mail
{
	/**
	 * Returns the context object
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	abstract protected function context() : \Aimeos\MShop\Context\Item\Iface;

	/**
	 * Prepares and returns a new mail message
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $addr Address item object
	 * @return \Aimeos\Base\Mail\Message\Iface Prepared mail message
	 */
	protected function mailTo( \Aimeos\MShop\Common\Item\Address\Iface $addr ) : \Aimeos\Base\Mail\Message\Iface
	{
		$context = $this->context();
		$config = $context->config();

		$message = $context->mail()->create()
			->header( 'X-MailGenerator', 'Aimeos' )
			->from( $config->get( 'resource/email/from-email' ), $config->get( 'resource/email/from-name' ) )
			->to( $addr->getEMail(), $addr->getFirstName() . ' ' . $addr->getLastName() );

		if( !empty( $email = $config->get( 'resource/email/bcc-email' ) ) ) {
			$message->bcc( $email );
		}

		return $message;
	}


	/**
	 * Returns the e-mail intro message
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $addr Address item object
	 * @return string Intro message with salutation
	 */
	protected function mailIntro( \Aimeos\MShop\Common\Item\Address\Iface $addr ) : string
	{
		switch( $addr->getSalutation() )
		{
			case \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_UNKNOWN:
				/// E-mail intro with first name (%1$s) and last name (%2$s)
				return $this->context()->translate( 'client', 'Dear %1$s %2$s' );
			case \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR:
				/// E-mail intro with first name (%1$s) and last name (%2$s)
				return $this->context()->translate( 'client', 'Dear Mr %1$s %2$s' );
			case \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MS:
				/// E-mail intro with first name (%1$s) and last name (%2$s)
				return $this->context()->translate( 'client', 'Dear Ms %1$s %2$s' );
		}

		return $this->context()->translate( 'client', 'Dear customer' );
	}


	/**
	 * Returns the view for generating the mail message content
	 *
	 * @param string|null $langId Language ID the content should be generated for
	 * @return \Aimeos\MW\View\Iface View object
	 */
	protected function mailView( string $langId = null ) : \Aimeos\MW\View\Iface
	{
		$view = $this->context()->view();

		$helper = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $this->context()->i18n( $langId ?: 'en' ) );
		$view->addHelper( 'translate', $helper );

		return $view;
	}
}
