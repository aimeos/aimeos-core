<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Admin_Log_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Controller_ExtJS_Admin_Log_Default( TestHelper::getContext() );
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


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'log.message' => 'unittest message' ) ) ) ),
			'sort' => 'log.timestamp',
			'dir' => 'DESC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 'unittest message', $result['items'][0]->{'log.message'} );
	}

	public function testDeleteItems()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->object->deleteItems( new stdClass() );
	}


	public function testSaveItems()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->object->saveItems( new stdClass() );
	}

}
