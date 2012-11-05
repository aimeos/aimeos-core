<?php

/**
 * Test class for MW_Session_CMSLite.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Session_Typo3Test extends MW_Unittest_Testcase
{
	/**
	 * @var    MW_Session_Typo3
	 * @access protected
	 */
	protected $_object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$mock = new Tslib_FeUserAuth();
		$this->_object = new MW_Session_Typo3($mock);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset($this->_object);
	}

	/**
	 * @todo Implement testGet().
	 */
	public function testGet()
	{
		$this->assertEquals('', $this->_object->get('test'));

		$this->_object->set('test', '123456789');
		$this->assertEquals('123456789', $this->_object->get('test'));
	}

	/**
	 * @todo Implement testSet().
	 */
	public function testSet()
	{
		$this->_object->set('test', '123');
		$this->assertEquals( '123', $this->_object->get( 'test' ) );

		$this->_object->set('test', '234');
		$this->assertEquals( '234', $this->_object->get( 'test' ) );
	}
}



class Tslib_FeUserAuth
{
	private $_session = array();

	public function getKey( $type , $key )
	{
		if ( isset($this->_session[$key]) ) {
			return $this->_session[$key];
		}
	}

	public function setKey( $type , $key , $data )
	{
		$this->_session[$key] = $data;
	}

	public function storeSessionData()
	{
	}
}
