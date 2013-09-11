<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */


class MW_Mail_ZendTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_mock;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		if( !class_exists( 'Zend_Mail' ) ) {
			$this->markTestSkipped( 'Zend_Mail is not available' );
		}

		$this->_mock = $this->getMockBuilder( 'Zend_Mail' )->disableOriginalConstructor()->getMock();
		$this->_object = new MW_Mail_Zend( $this->_mock );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
	}


	public function testCreateMessage()
	{
		$result = $this->_object->createMessage( 'ISO-8859-1' );
		$this->assertInstanceOf( 'MW_Mail_Message_Interface', $result );
	}


	public function testSend()
	{
		$this->_mock->expects( $this->once() )->method( 'send' );

		$this->_object->send( $this->_object->createMessage() );
	}

}
