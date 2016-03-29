<?php

namespace Aimeos\Controller\Jobs\Customer\Email\Account;


/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;
	private $aimeos;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = \TestHelperJobs::getContext();
		$this->aimeos = \TestHelperJobs::getAimeos();

		$this->object = new \Aimeos\Controller\Jobs\Customer\Email\Account\Standard( $this->context, $this->aimeos );
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


	public function testGetName()
	{
		$this->assertEquals( 'Customer account e-mails', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Sends e-mails for new customer accounts';
		$this->assertEquals( $text, $this->object->getDescription() );
	}


	public function testRun()
	{
		$mailStub = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\None' )
			->disableOriginalConstructor()
			->getMock();

		$mailMsgStub = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\Message\\None' )
			->disableOriginalConstructor()
			->disableOriginalClone()
			->getMock();

		$mailStub->expects( $this->once() )
			->method( 'createMessage' )
			->will( $this->returnValue( $mailMsgStub ) );

		$mailStub->expects( $this->once() )->method( 'send' );
		$this->context->setMail( $mailStub );


		$queueStub = $this->getMockBuilder( '\\Aimeos\\MW\\MQueue\\Queue\\Standard' )
			->disableOriginalConstructor()
			->getMock();

		$queueStub->expects( $this->exactly( 2 ) )->method( 'get' )
			->will( $this->onConsecutiveCalls( new \Aimeos\MW\MQueue\Message\Standard( array( 'message' => '{}') ), null ) );

		$queueStub->expects( $this->once() )->method( 'del' );


		$mqueueStub = $this->getMockBuilder( '\\Aimeos\\MW\\MQueue\\Standard' )
			->disableOriginalConstructor()
			->getMock();

		$mqueueStub->expects( $this->once() )->method( 'getQueue' )
			->will( $this->returnValue( $queueStub ) );


		$managerStub = $this->getMockBuilder( '\\Aimeos\\MW\\MQueue\\Manager\\Standard' )
			->disableOriginalConstructor()
			->getMock();

		$managerStub->expects( $this->once() )->method( 'get' )
			->will( $this->returnValue( $mqueueStub ) );

		$this->context->setMessageQueueManager( $managerStub );


		$this->object->run();
	}
}
