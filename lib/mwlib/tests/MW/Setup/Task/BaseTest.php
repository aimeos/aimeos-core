<?php


class MW_Setup_Task_BaseImpl extends MW_Setup_Task_Base
{
	public function getPreDependencies()
	{
		return array( 'TestTask' );
	}

	public function getPostDependencies()
	{
		return array();
	}

	protected function mysql()
	{
		$this->execute( 'SELECT 1+1' );

		$list = array(
			'SELECT 1+2',
			'SELECT 1+3',
		);

		$this->executeList( $list );
	}
}


/**
 * Test class for MW_Setup_Task_Base.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Setup_Task_BaseTest extends PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$config = TestHelper::getConfig();

		if( $config->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$dbm = TestHelper::getDBManager();
		$conn = $dbm->acquire();

		$schema = new MW_Setup_DBSchema_Mysql( $conn, $config->get( 'resource/db/database', 'notfound' ) );
		$this->object = new MW_Setup_Task_BaseImpl( $schema, $conn );

		$dbm->release( $conn );
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

	public function testGetPreDependencies()
	{
		$this->assertEquals( array( 'TestTask' ), $this->object->getPreDependencies() );
	}

	public function testGetPostDependencies()
	{
		$this->assertEquals( array(), $this->object->getPostDependencies() );
	}

	public function testRun()
	{
		$this->object->run( 'mysql' );

		$this->setExpectedException( 'MW_Setup_Exception' );
		$this->object->run( 'notexisting' );
	}
}
