<?php

namespace Aimeos\MW\MQueue\Queue;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private static $dbm;
	private $object;


	public static function setUpBeforeClass()
	{
		self::$dbm = \TestHelperMw::getDBManager();

		if( !( self::$dbm instanceof \Aimeos\MW\DB\Manager\DBAL ) ) {
			return;
		}

		$schema = new \Doctrine\DBAL\Schema\Schema();

		$table = $schema->createTable( 'mw_mqueue_test' );
		$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
		$table->addColumn( 'queue', 'string', array( 'length' => 255 ) );
		$table->addColumn( 'cname', 'string', array( 'length' => 32 ) );
		$table->addColumn( 'rtime', 'datetime', [] );
		$table->addColumn( 'message', 'text', array( 'length' => 0xffff ) );
		$table->setPrimaryKey( array( 'id' ) );


		$conn = self::$dbm->acquire();

		foreach( $schema->toSQL( $conn->getRawObject()->getDatabasePlatform() ) as $sql ) {
			$conn->create( $sql )->execute()->finish();
		}

		self::$dbm->release( $conn );
	}


	public static function tearDownAfterClass()
	{
		if( self::$dbm instanceof \Aimeos\MW\DB\Manager\DBAL )
		{
			$conn = self::$dbm->acquire();

			$conn->create( 'DROP TABLE "mw_mqueue_test"' )->execute()->finish();

			self::$dbm->release( $conn );
		}
	}


	protected function setUp()
	{
		$config = array(
			'db' => \TestHelperMw::getConfig()->get( 'resource/db' ),
			'sql' => array(
				'insert' => 'INSERT INTO mw_mqueue_test (queue, cname, rtime, message) VALUES (?, ?, ?, ?)',
				'reserve' => 'UPDATE mw_mqueue_test SET cname = ?, rtime = ? WHERE id IN ( SELECT * FROM ( SELECT id FROM mw_mqueue_test WHERE queue = ? AND rtime < ? LIMIT 1 ) AS t )',
				'get' => 'SELECT * FROM mw_mqueue_test WHERE queue = ? AND cname = ? AND rtime = ? LIMIT 1',
				'delete' => 'DELETE FROM mw_mqueue_test WHERE id = ? AND queue = ?',
			),
		);
		$mqueue = new \Aimeos\MW\MQueue\Standard( $config );
		$this->object = $mqueue->getQueue( 'email' );
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
