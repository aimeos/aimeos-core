<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */


/**
 * Test class for MW_View_Helper_Mail.
 */
class MW_View_Helper_Mail_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $message;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$view = new MW_View_Default();

		$mail = new MW_Mail_None();
		$this->message = $mail->createMessage();

		$this->object = new MW_View_Helper_Mail_Default( $view, $this->message );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testTransform()
	{
		$this->assertSame( $this->message, $this->object->transform() );
	}

}
