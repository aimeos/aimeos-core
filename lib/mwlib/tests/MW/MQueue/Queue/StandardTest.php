<?php

namespace Aimeos\MW\MQueue\Queue;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private static $dbm;
	private $object;


	public static function setUpBeforeClass() : void
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


	public static function tearDownAfterClass() : void
	{
		if( self::$dbm instanceof \Aimeos\MW\DB\Manager\DBAL )
		{
			$conn = self::$dbm->acquire();

			$conn->create( 'DROP TABLE "mw_mqueue_test"' )->execute()->finish();

			self::$dbm->release( $conn );
		}
	}


	protected function setUp() : void
	{
		$config = array(
			'db' => \TestHelperMw::getConfig()->get( 'resource/db' ),
			'sql' => array(
				'insert' => 'INSERT INTO mw_mqueue_test (queue, cname, rtime, message) VALUES (?, ?, ?, ?)',
				'delete' => 'DELETE FROM mw_mqueue_test WHERE id = ? AND queue = ?',
			),
		);

		if( \TestHelperMw::getConfig()->get( 'resource/db/adapter' ) === 'mysql' )
		{
			$config['sql']['reserve'] = '
				UPDATE mw_mqueue_test SET cname = ?, rtime = ? WHERE id IN (
					SELECT id FROM (
						SELECT id FROM mw_mqueue_test WHERE queue = ? AND rtime < ? ORDER BY id LIMIT 1
					) AS t
				)
			';
			$config['sql']['get'] = '
				SELECT * FROM mw_mqueue_test WHERE queue = ? AND cname = ? AND rtime = ?
				ORDER BY id LIMIT 1
			';
		}
		else
		{
			$config['sql']['reserve'] = '
				UPDATE mw_mqueue_test SET cname = ?, rtime = ? WHERE id IN (
					SELECT id FROM (
						SELECT id FROM mw_mqueue_test WHERE queue = ? AND rtime < ?
						ORDER BY id OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY
					) AS t
				)
			';
			$config['sql']['get'] = '
				SELECT * FROM mw_mqueue_test WHERE queue = ? AND cname = ? AND rtime = ?
				ORDER BY id OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY
			';
		}

		$mqueue = new \Aimeos\MW\MQueue\Standard( $config );
		$this->object = $mqueue->getQueue( 'email' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testProcess()
	{
		$this->object->add( 'test' );
		$msg = $this->object->get();

		$this->assertInstanceOf( \Aimeos\MW\MQueue\Message\Iface::class, $msg );

		$this->object->del( $msg );

		$this->assertNull( $this->object->get() );
	}


	public function testProcessMultiple()
	{
		$this->object->add( 'test1' );
		$this->object->add( 'test2' );

		$msg1 = $this->object->get();

		$this->assertInstanceOf( \Aimeos\MW\MQueue\Message\Iface::class, $msg1 );

		$this->object->del( $msg1 );

		$msg2 = $this->object->get();

		$this->assertInstanceOf( \Aimeos\MW\MQueue\Message\Iface::class, $msg2 );

		$this->object->del( $msg2 );

		$this->assertNull( $this->object->get() );
		$this->assertTrue( $msg1->getBody() !== $msg2->getBody() );
	}
}
