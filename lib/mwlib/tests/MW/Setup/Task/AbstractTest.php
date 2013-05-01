<?php




class MW_Setup_Task_AbstractImpl extends MW_Setup_Task_Abstract
{
	public function getPreDependencies()
	{
		return array( 'TestTask' );
	}

	public function getPostDependencies()
	{
		return array();
	}

	protected function _mysql()
	{
		$this->_execute( 'SELECT 1+1' );

		$list = array(
			'SELECT 1+2',
			'SELECT 1+3',
		);

		$this->_executeList( $list );
	}
}


/**
 * Test class for MW_Setup_Task_Abstract.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Setup_Task_AbstractTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$config = MW_TestHelper::getConfig();

		if( $config->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$dbm = MW_TestHelper::getDBManager();
		$conn = $dbm->acquire();

		$schema = new MW_Setup_DBSchema_Mysql( $conn, $config->get( 'resource/db/database', 'notfound' ) );
		$this->_object = new MW_Setup_Task_AbstractImpl( $schema, $conn );

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
		$this->assertEquals( array( 'TestTask' ), $this->_object->getPreDependencies() );
	}

	public function testGetPostDependencies()
	{
		$this->assertEquals( array(), $this->_object->getPostDependencies() );
	}

	public function testRun()
	{
		$this->_object->run( 'mysql' );

		$this->setExpectedException( 'MW_Setup_Exception' );
		$this->_object->run( 'notexisting' );
	}
}
