<?php

namespace Aimeos\Controller\Jobs\Admin\Log;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();

		$this->object = new \Aimeos\Controller\Jobs\Admin\Log\Standard( $this->context, $aimeos );
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
		\Aimeos\MShop\Factory::clear();
	}


	public function testGetName()
	{
		$this->assertEquals( 'Log cleanup', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Removes the old log entries from the database and archives them (optional)';
		$this->assertEquals( $text, $this->object->getDescription() );
	}


	public function testRun()
	{
		$config = $this->context->getConfig();

		$mock = $this->getMockBuilder( '\\Aimeos\\MAdmin\\Log\\Manager\\Standard' )
			->setMethods( array( 'deleteItems' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$mock->expects( $this->atLeastOnce() )->method( 'deleteItems' );

		$tmppath = dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) . DIRECTORY_SEPARATOR . 'tmp';
		$name = 'ControllerJobsAdminLogDefaultRun';
		$config->set( 'madmin/log/manager/name', $name );
		$config->set( 'controller/jobs/admin/log/standard/limit-days', 0 );
		$config->set( 'controller/jobs/admin/log/standard/path', $tmppath );

		\Aimeos\MAdmin\Log\Manager\Factory::injectManager( '\\Aimeos\\MAdmin\\Log\\Manager\\' . $name, $mock );

		if( !is_dir( $tmppath ) && mkdir( $tmppath ) === false ) {
			throw new \Exception( sprintf( 'Unable to create temporary path "%1$s"', $tmppath ) );
		}

		$this->object->run();

		foreach( new \DirectoryIterator( $tmppath ) as $file )
		{
			if( $file->isFile() && $file->getExtension() === 'zip' )
			{
				$container = \Aimeos\MW\Container\Factory::getContainer( $file->getPathName(), 'Zip', 'CSV', array() );
				$container->get( 'unittest facility.csv' );
				unlink( $file->getPathName() );
				return;
			}
		}

		$this->fail( 'Log archive file not found' );
	}
}
