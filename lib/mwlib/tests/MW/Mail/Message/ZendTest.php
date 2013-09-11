<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */


class MW_Mail_Message_ZendTest extends MW_Unittest_Testcase
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
		$this->_object = new MW_Mail_Message_Zend( $this->_mock );
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


	public function testAddFrom()
	{
		$this->_mock->expects( $this->once() )->method( 'setFrom' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->addFrom( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testAddTo()
	{
		$this->_mock->expects( $this->once() )->method( 'addTo' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->addTo( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testAddCc()
	{
		$this->_mock->expects( $this->once() )->method( 'addCc' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->addCc( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testAddBcc()
	{
		$this->_mock->expects( $this->once() )->method( 'addBcc' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->addBcc( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testSetReplyTo()
	{
		$this->_mock->expects( $this->once() )->method( 'setReplyTo' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->setReplyTo( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testSetSender()
	{
		$this->_mock->expects( $this->once() )->method( 'setFrom' )
			->with( $this->stringContains( 'a@b' ), $this->stringContains( 'test' ) );

		$result = $this->_object->setSender( 'a@b', 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testSetSubject()
	{
		$this->_mock->expects( $this->once() )->method( 'setSubject' )
			->with( $this->stringContains( 'test' ) );

		$result = $this->_object->setSubject( 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testSetBody()
	{
		$this->_mock->expects( $this->once() )->method( 'setBodyText' )
			->with( $this->stringContains( 'test' ) );

		$result = $this->_object->setBody( 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testSetBodyHtml()
	{
		$this->_mock->expects( $this->once() )->method( 'setBodyHtml' )
			->with( $this->stringContains( 'test' ) );

		$result = $this->_object->setBodyHtml( 'test' );
		$this->assertSame( $this->_object, $result );
	}


	public function testAddAttachment()
	{
		$partMock = $this->getMockBuilder( 'Zend_Mime_Part' )->disableOriginalConstructor()->getMock();

		$this->_mock->expects( $this->once() )->method( 'createAttachment' )
			->with( $this->stringContains( 'test' ), $this->stringContains( 'text/plain' ),
				$this->stringContains( 'test.txt' ), $this->stringContains( 'inline' ) )
			->will( $this->returnValue( $partMock ) );

		$this->_mock->expects( $this->once() )->method( 'addAttachment' );

		$result = $this->_object->addAttachment( 'test', 'text/plain', 'test.txt', 'inline' );
		$this->assertSame( $this->_object, $result );
	}


	public function testGetObject()
	{
		$this->assertInstanceOf( 'Zend_Mail', $this->_object->getObject() );
	}
}
