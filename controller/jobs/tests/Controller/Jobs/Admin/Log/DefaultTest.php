<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_Jobs_Admin_Log_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->_object = new Controller_Jobs_Admin_Log_Default( $this->_context, $arcavias );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
		MShop_Factory::clear();
	}


	public function testGetName()
	{
		$this->assertEquals( 'Log cleanup', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Removes the old log entries from the database and archives them (optional)';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$config = $this->_context->getConfig();

		$mock = $this->getMockBuilder( 'MAdmin_Log_Manager_Default' )
			->setMethods( array( 'deleteItems' ) )
			->setConstructorArgs( array( $this->_context ) )
			->getMock();

		$mock->expects( $this->atLeastOnce() )->method( 'deleteItems' );

		$tmppath = dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) . DIRECTORY_SEPARATOR . 'tmp';
		$name = 'ControllerJobsAdminLogDefaultRun';
		$config->set( 'classes/log/manager/name', $name );
		$config->set( 'controller/jobs/admin/log/default/limit-days', 0 );
		$config->set( 'controller/jobs/admin/log/default/path', $tmppath );

		MAdmin_Log_Manager_Factory::injectManager( 'MAdmin_Log_Manager_' . $name, $mock );

		if( !is_dir( $tmppath ) && mkdir( $tmppath ) === false ) {
			throw new Exception( sprintf( 'Unable to create temporary path "%1$s"', $tmppath ) );
		}

		$this->_object->run();

		foreach( new DirectoryIterator( $tmppath ) as $file )
		{
			if( $file->isFile() && $file->getExtension() === 'zip' )
			{
				$container = MW_Container_Factory::getContainer( $file->getPathName(), 'Zip', 'CSV', array() );
				$container->get( 'unittest facility.csv' );
				unlink( $file->getPathName() );
				return;
			}
		}

		$this->fail( 'Log archive file not found' );
	}
}
