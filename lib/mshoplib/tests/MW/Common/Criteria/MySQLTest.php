<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

namespace Aimeos\MW\Common\Criteria;


/**
 * Test class for MySQL search criteria class.
 */
class MySQLTest extends \PHPUnit_Framework_TestCase
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
		$context = \TestHelper::getContext( 'unit' );
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		$this->object = new \Aimeos\MW\Common\Criteria\MySQL( $conn );

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
		unset( $this->object );
	}


	public function testCreateFunction()
	{
		$params = array( 'listtype', 'langid', 'test string' );

		$str = $this->object->createFunction( 'index.text.relevance', $params );
		$this->assertEquals( 'index.text.relevance("listtype","langid"," +test* +string*")', $str );

		$str = $this->object->createFunction( 'sort:index.text.relevance', $params );
		$this->assertEquals( 'sort:index.text.relevance("listtype","langid"," +test* +string*")', $str );
	}

}
