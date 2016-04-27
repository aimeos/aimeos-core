<?php

namespace Aimeos\MW\MQueue\Queue;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	public static function setUpBeforeClass()
	{
		$config = \TestHelperMw::getConfig();

		if( ( $adapter = $config->get( 'resource/db/adapter', false ) ) === false ) {
			self::markTestSkipped( 'No database configured' );
		}

		$db = new \Aimeos\MW\DB\Manager\PDO( $config );

		$sql = 'CREATE TABLE mw_mqueue_test (
			id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
			queue VARCHAR(255) NOT NULL,
			cname VARCHAR(32) NOT NULL,
			rtime DATETIME NOT NULL,
			message TEXT NOT NULL )
		';

		$conn = $db->acquire();
		$conn->create( $sql )->execute()->finish();
		$db->release( $conn );
	}


	public static function tearDownAfterClass()
	{
		$config = \TestHelperMw::getConfig();

		if( ( $adapter = $config->get( 'resource/db/adapter', false ) ) === false ) {
			self::markTestSkipped( 'No database configured' );
		}

		$db = new \Aimeos\MW\DB\Manager\PDO( $config );

		$conn = $db->acquire();
		$conn->create( 'DROP TABLE "mw_mqueue_test"' )->execute()->finish();
		$db->release( $conn );
	}


	protected function setUp()
	{
		$config = array(
			'db' => \TestHelperMw::getConfig()->get( 'resource/db' ),
			'sql' => array(
				'insert' => 'INSERT INTO mw_mqueue_test (queue, cname, rtime, message) VALUES (?, ?, ?, ?)',
				'reserve' => 'UPDATE mw_mqueue_test SET cname = ?, rtime = ? WHERE queue = ? AND rtime < ? LIMIT 1',
				'get' => 'SELECT * FROM mw_mqueue_test WHERE queue = ? AND cname = ? AND rtime = ? LIMIT 1',
				'delete' => 'DELETE FROM mw_mqueue_test WHERE id = ? AND queue = ?',
			),
		);
		$mqeue = new \Aimeos\MW\MQueue\Standard( $config );
		$this->object = $mqeue->getQueue( 'email' );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testProcess()
	{
		$this->object->add( 'test' );
		$msg = $this->object->get();

		$this->assertInstanceOf( '\Aimeos\MW\MQueue\Message\Iface', $msg );

		$this->object->del( $msg );

		$this->assertNull( $this->object->get() );
	}


	public function testProcessMultiple()
	{
		$this->object->add( 'test1' );
		$this->object->add( 'test2' );

		$msg1 = $this->object->get();

		$this->assertInstanceOf( '\Aimeos\MW\MQueue\Message\Iface', $msg1 );

		$this->object->del( $msg1 );

		$msg2 = $this->object->get();

		$this->assertInstanceOf( '\Aimeos\MW\MQueue\Message\Iface', $msg2 );

		$this->object->del( $msg2 );

		$this->assertNull( $this->object->get() );
		$this->assertTrue( $msg1->getBody() !== $msg2->getBody() );
	}
}
