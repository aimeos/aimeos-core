<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\View\Helper\Mail;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $message;


	protected function setUp() : void
	{
		$view = new \Aimeos\MW\View\Standard();

		$mail = new \Aimeos\MW\Mail\None();
		$this->message = $mail->createMessage();

		$this->object = new \Aimeos\MW\View\Helper\Mail\Standard( $view, $this->message );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testTransform()
	{
		$this->assertSame( $this->message, $this->object->transform() );
	}

}
