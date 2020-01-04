<?php

namespace Aimeos\MW\Tree\Manager;


class DBNestedSetTest extends \PHPUnit\Framework\TestCase
{
	private static $dbm;
	private $config;


	public static function setUpBeforeClass() : void
	{
		self::$dbm = \TestHelperMw::getDBManager();

		if( !( self::$dbm instanceof \Aimeos\MW\DB\Manager\DBAL ) ) {
			return;
		}

		$schema = new \Doctrine\DBAL\Schema\Schema();

		$table = $schema->createTable( 'mw_tree_test' );
		$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
		$table->addColumn( 'parentid', 'integer', array( 'notnull' => false ) );
		$table->addColumn( 'label', 'string', array( 'length' => 16 ) );
		$table->addColumn( 'code', 'string', array( 'length' => 32 ) );
		$table->addColumn( 'level', 'integer', [] );
		$table->addColumn( 'nleft', 'integer', [] );
		$table->addColumn( 'nright', 'integer', [] );
		$table->addColumn( 'status', 'smallint', [] );
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

			$conn->create( 'DROP TABLE "mw_tree_test"' )->execute()->finish();

			self::$dbm->release( $conn );
		}
	}


	protected function setUp() : void
	{
		if( !( self::$dbm instanceof \Aimeos\MW\DB\Manager\DBAL ) ) {
			$this->markTestSkipped( 'No DBAL database manager configured' );
		}

		$this->config = [];

		$this->config['search'] = array(
			'id' => array( 'label' => 'Tree node ID', 'code' => 'tree.id', 'internalcode' => 'id', 'type' => 'integer', 'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT ),
			'parentid' => array( 'label' => 'Tree node parent id', 'code' => 'tree.parentid', 'internalcode' => 'parentid', 'type' => 'integer', 'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT ),
			'label' => array( 'label' => 'Tree node name', 'code' => 'tree.label', 'internalcode' => 'label', 'type' => 'string', 'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR ),
			'code' => array( 'label' => 'Tree node code', 'code' => 'tree.code', 'internalcode' => 'code', 'type' => 'string', 'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR ),
			'status' => array( 'label' => 'Tree node status', 'code' => 'tree.status', 'internalcode' => 'status', 'type' => 'boolean', 'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT ),
			'level' => array( 'label' => 'Tree node level', 'code' => 'tree.level', 'internalcode' => 'level', 'type' => 'integer', 'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT ),
			'left' => array( 'label' => 'Tree node left number', 'code' => 'tree.left', 'internalcode' => 'nleft', 'type' => 'integer', 'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT ),
			'right' => array( 'label' => 'Tree node right number', 'code' => 'tree.right', 'internalcode' => 'nright', 'type' => 'integer', 'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT ),

		);

		$this->config['sql'] = array(
			'delete' => '
				DELETE FROM "mw_tree_test" WHERE nleft >= ? AND nright <= ?
			',
			'get' => '
				SELECT
					node."id", node."label", node."code", node."status", node."level",
					node."parentid", node."nleft" AS "left", node."nright" AS "right"
				FROM "mw_tree_test" AS parent, "mw_tree_test" AS node
				WHERE
					node.nleft >= parent.nleft AND node.nleft <= parent.nright
					AND parent.id = ? AND node.level <= parent.level + ?
					AND :cond
				ORDER BY node.nleft
			',
			'insert' => '
				INSERT INTO "mw_tree_test" ( label, code, status, parentid, level, nleft, nright ) VALUES ( ?, ?, ?, ?, ?, ?, ? )
			',
			'move-left' => '
				UPDATE "mw_tree_test"
				SET nleft = nleft + ?, level = level + ?
				WHERE nleft >= ? AND nleft <= ?
			',
			'move-right' => '
				UPDATE "mw_tree_test"
				SET nright = nright + ?
				WHERE nright >= ? AND nright <= ?
			',
			'search' => '
				SELECT "id", "label", "code", "status", "level", "nleft" AS "left", "nright" AS "right"
				FROM "mw_tree_test" AS node
				WHERE nleft >= ? AND nright <= ? AND :cond
				ORDER BY :order
			',
			'update' => '
				UPDATE "mw_tree_test" SET label = ?, code = ?, status = ? WHERE id = ?
			',
			'update-parentid' => '
				UPDATE "mw_tree_test" SET parentid = ? WHERE id = ?
			',
			'transstart' => 'BEGIN',
			'transcommit' => 'COMMIT',
			'transrollback' => 'ROLLBACK',
		);

		switch( \TestHelperMw::getConfig()->get( 'resource/db/adapter' ) )
		{
			case 'mysql': $this->config['sql']['newid'] = 'SELECT LAST_INSERT_ID()'; break;
			case 'pgsql': $this->config['sql']['newid'] = 'SELECT lastval()'; break;
			default:
				$this->markTestSkipped( 'Only for MySQL and PostgreSQL' );
		}


		$conn = self::$dbm->acquire();

		$sql = 'INSERT INTO "mw_tree_test" (parentid, status, label, code, level, nleft, nright) VALUES (0, 1, \'root\', \'root\', 0, 1, 18)';
		$conn->create( $sql )->execute()->finish();

		$sql = 'INSERT INTO "mw_tree_test" (status, label, code, level, nleft, nright) VALUES (1, \'l1n1\', \'l1n1\', 1, 2, 7)';
		$conn->create( $sql )->execute()->finish();

		$sql = 'INSERT INTO "mw_tree_test" (status, label, code, level, nleft, nright) VALUES (1, \'l2n1\', \'l2n1\', 2, 3, 6)';
		$conn->create( $sql )->execute()->finish();

		$sql = 'INSERT INTO "mw_tree_test" (status, label, code, level, nleft, nright) VALUES (1, \'l3n1\', \'l3n1\', 3, 4, 5)';
		$conn->create( $sql )->execute()->finish();

		$sql = 'INSERT INTO "mw_tree_test" (status, label, code, level, nleft, nright) VALUES (1, \'l1n2\', \'l1n2\', 1, 8, 15)';
		$conn->create( $sql )->execute()->finish();

		$sql = 'INSERT INTO "mw_tree_test" (status, label, code, level, nleft, nright) VALUES (1, \'l2n2\', \'l2n2\', 2, 9, 14)';
		$conn->create( $sql )->execute()->finish();

		$sql = 'INSERT INTO "mw_tree_test" (status, label, code, level, nleft, nright) VALUES (1, \'l3n2\', \'l3n2\', 3, 10, 11)';
		$conn->create( $sql )->execute()->finish();

		$sql = 'INSERT INTO "mw_tree_test" (status, label, code, level, nleft, nright) VALUES (1, \'l3n3\', \'l3n3\', 3, 12, 13)';
		$conn->create( $sql )->execute()->finish();

		$sql = 'INSERT INTO "mw_tree_test" (status, label, code, level, nleft, nright) VALUES (1, \'l1n3\', \'l1n3\', 1, 16, 17)';
		$conn->create( $sql )->execute()->finish();

		$sql = 'INSERT INTO "mw_tree_test" (status, label, code, level, nleft, nright) VALUES (1, \'root2\', \'root2\', 0, 19, 20)';
		$conn->create( $sql )->execute()->finish();

		self::$dbm->release( $conn );
	}


	protected function tearDown() : void
	{
		if( self::$dbm instanceof \Aimeos\MW\DB\Manager\DBAL )
		{
			$conn = self::$dbm->acquire();

			$conn->create( 'DELETE FROM "mw_tree_test"' )->execute()->finish();

			self::$dbm->release( $conn );
		}
	}


	public function testConstructorNoDatabaseManager()
	{
		$this->expectException( \Aimeos\MW\Tree\Exception::class );
		new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, null );
	}


	public function testConstructorNoConfig()
	{
		$this->expectException( \Aimeos\MW\Tree\Exception::class );
		new \Aimeos\MW\Tree\Manager\DBNestedSet( [], self::$dbm );
	}


	public function testConstructorNoSqlConfig()
	{
		unset( $this->config['sql'] );

		$this->expectException( \Aimeos\MW\Tree\Exception::class );
		new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
	}


	public function testConstructorMissingSqlConfig()
	{
		unset( $this->config['sql']['newid'] );

		$this->expectException( \Aimeos\MW\Tree\Exception::class );
		new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
	}


	public function testConstructorMissingSearchConfig()
	{
		unset( $this->config['search']['id'] );

		$this->expectException( \Aimeos\MW\Tree\Exception::class );
		new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
	}


	public function testGetSearchAttributes()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );

		foreach( $manager->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testIsReadOnly()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );

		$this->assertFalse( $manager->isReadOnly() );
	}


	public function testCreateSearch()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );

		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $manager->createSearch() );
	}


	public function testSearchNodes()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$search = $manager->createSearch();


		$search->setConditions( $search->compare( '==', 'tree.level', 1 ) );
		$nodes = $manager->searchNodes( $search );

		$this->assertEquals( 3, count( $nodes ) );

		foreach( $nodes as $node ) {
			$this->assertInstanceOf( \Aimeos\MW\Tree\Node\Iface::class, $node );
		}


		if( ( $node = reset( $nodes ) ) === false ) {
			throw new \RuntimeException( 'No node found' );
		}

		$search->setConditions( $search->compare( '==', 'tree.level', 3 ) );
		$nodes = $manager->searchNodes( $search, $node->getId() );

		$this->assertEquals( 1, count( $nodes ) );

		foreach( $nodes as $node ) {
			$this->assertInstanceOf( \Aimeos\MW\Tree\Node\Iface::class, $node );
		}
	}


	public function testSearchException()
	{
		$this->config['sql']['search'] = '
			SELECT "id", "label", "code", "status", "level", "domain" as "base", "left" AS "left", "right" AS "right"
			FROM "mw_tree_test"
			WHERE domain123 = ? AND nleft >= ? AND nright <= ? AND :cond
		';

		$this->expectException( \Aimeos\MW\DB\Exception::class );

		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$manager->searchNodes( $manager->createSearch() );
	}


	public function testDeleteNode()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'tree.label', 'l2n2' ) );
		$nodes = $manager->searchNodes( $search );
		$this->assertEquals( 1, count( $nodes ) );

		$this->assertInstanceOf( \Aimeos\MW\Tree\Manager\Iface::class, $manager->deleteNode( reset( $nodes )->getId() ) );

		$search = $manager->createSearch();
		$nodes = $manager->searchNodes( $search );
		$this->assertEquals( 7, count( $nodes ) );
	}


	public function testDeleteNodeException()
	{
		$this->config['sql']['search'] = '
			DELETE FROM "mw_tree_test" WHERE domain = ? AND nleft12 >= ? AND nright <= ?
		';

		$this->expectException( \Aimeos\MW\DB\Exception::class );

		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );
		$manager->deleteNode( $root->getId() );
	}


	public function testGetNode()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$search = $manager->createSearch();

		$node = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE, $search );

		$this->assertEquals( 0, $node->level );
		$this->assertEquals( 0, count( $node->getChildren() ) );
	}


	public function testGetNodeList()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );

		$node = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );
		$node = $manager->getNode( $node->getId(), \Aimeos\MW\Tree\Manager\Base::LEVEL_LIST );

		$this->assertEquals( 3, count( $node->getChildren() ) );
		$this->assertEquals( 0, count( $node->getChild( 0 )->getChildren() ) );
		$this->assertEquals( 0, count( $node->getChild( 1 )->getChildren() ) );
	}


	public function testGetNodeTree()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );

		$node = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );
		$node = $manager->getNode( $node->getId(), \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$this->assertEquals( 3, count( $node->getChildren() ) );
		$this->assertEquals( 1, count( $node->getChild( 0 )->getChildren() ) );
		$this->assertEquals( 1, count( $node->getChild( 0 )->getChild( 0 )->getChildren() ) );
		$this->assertEquals( 1, count( $node->getChild( 1 )->getChildren() ) );
		$this->assertEquals( 2, count( $node->getChild( 1 )->getChild( 0 )->getChildren() ) );
		$this->assertEquals( 0, count( $node->getChild( 2 )->getChildren() ) );
	}


	public function testGetPath()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );

		$nodes = [];
		$nodes[0] = $manager->getNode();
		$nodes[1] = $nodes[0]->getChild( 1 );
		$nodes[2] = $nodes[1]->getChild( 0 );
		$nodes[3] = $nodes[2]->getChild( 1 );

		$path = $manager->getPath( (string) $nodes[3]->getId() );

		foreach( $nodes as $node )
		{
			if( ( $actual = array_shift( $path ) ) === null ) {
				throw new \RuntimeException( 'Not enough nodes in path' );
			}

			$this->assertEquals( $node->getId(), $actual->getId() );
		}
	}


	public function testGetLevelFromConstantException()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );

		$this->expectException( \Aimeos\MW\Tree\Exception::class );
		$manager->getNode( null, 0 );
	}


	public function testInsertNode()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );

		$newNode = $manager->createNode();
		$newNode->setLabel( 'l1n4' );
		$manager->insertNode( $newNode, $root->getId() );

		$root = $manager->getNode( $root->getId(), \Aimeos\MW\Tree\Manager\Base::LEVEL_LIST );
		$this->assertEquals( 4, count( $root->getChildren() ) );
		$this->assertEquals( 'l1n4', $root->getChild( 3 )->getLabel() );
		$this->assertEquals( $newNode->getId(), $root->getChild( 3 )->getId() );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'tree.label', 'l1n3' ) );
		$nodes = $manager->searchNodes( $search );
		$this->assertEquals( 1, count( $nodes ) );

		$newNode->setLabel( 'new l1n3' );
		$newNode = $manager->insertNode( $newNode, $root->getId(), reset( $nodes )->getId() );

		$root = $manager->getNode( $root->getId(), \Aimeos\MW\Tree\Manager\Base::LEVEL_LIST );
		$this->assertInstanceOf( \Aimeos\MW\Tree\Node\Iface::class, $newNode );
		$this->assertEquals( 5, count( $root->getChildren() ) );
		$this->assertEquals( 'l1n2', $root->getChild( 1 )->getLabel() );
		$this->assertEquals( 'new l1n3', $root->getChild( 2 )->getLabel() );
		$this->assertEquals( 'l1n3', $root->getChild( 3 )->getLabel() );
		$this->assertEquals( 'l1n4', $root->getChild( 4 )->getLabel() );
	}


	public function testInsertNodeException()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$newNode = $manager->createNode();

		$this->expectException( \Aimeos\MW\Tree\Exception::class );
		$manager->insertNode( $newNode, -1 );
	}


	public function testInsertNodeRoot()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );

		$newNode = $manager->createNode();
		$newNode->setCode( 'root3' );
		$newNode->setLabel( 'Root 3' );

		$this->assertInstanceOf( \Aimeos\MW\Tree\Node\Iface::class, $manager->insertNode( $newNode ) );

		$root = $manager->getNode( $newNode->getId() );
		$this->assertEquals( 'Root 3', $root->getLabel() );
		$this->assertEquals( 21, $root->left );
		$this->assertEquals( 22, $root->right );
	}


	public function testMoveNodeNoParent()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$nodeid = $root->getChild( 0 )->getChild( 0 )->getChild( 0 )->getId();
		$oldparentid = $root->getChild( 0 )->getChild( 0 )->getId();

		$result = $manager->moveNode( (string) $nodeid, $oldparentid, null );
		$this->assertInstanceOf( \Aimeos\MW\Tree\Manager\Iface::class, $result );

		$testroot = $manager->getNode( $nodeid, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$this->assertEquals( 0, $testroot->level );
		$this->assertEquals( 19, $testroot->left );
		$this->assertEquals( 20, $testroot->right );
		$this->assertEquals( 0, count( $testroot->getChildren() ) );
	}


	public function testMoveNodeSameParent()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$nodeid = $root->getChild( 0 )->getId();
		$oldparentid = $root->getId();

		$manager->moveNode( (string) $nodeid, $oldparentid, $oldparentid );
		$manager->moveNode( (string) $nodeid, $oldparentid, $oldparentid );

		$testroot = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$this->assertEquals( 'l1n1', $testroot->getChild( 2 )->label );
		$this->assertEquals( 1, $testroot->getChild( 2 )->level );
		$this->assertEquals( 12, $testroot->getChild( 2 )->left );
		$this->assertEquals( 17, $testroot->getChild( 2 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 2 )->getChildren() ) );
	}


	public function testMoveNode1()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$nodeid = $root->getChild( 0 )->getChild( 0 )->getChild( 0 )->getId();
		$oldparentid = $root->getChild( 0 )->getChild( 0 )->getId();
		$newparentid = $root->getChild( 0 )->getId();
		$refnodeid = null;

		$manager->moveNode( (string) $nodeid, $oldparentid, $newparentid, $refnodeid );

		$testroot = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$this->assertEquals( 0, $testroot->level );
		$this->assertEquals( 1, $testroot->left );
		$this->assertEquals( 18, $testroot->right );
		$this->assertEquals( 3, count( $testroot->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 0 )->level );
		$this->assertEquals( 2, $testroot->getChild( 0 )->left );
		$this->assertEquals( 7, $testroot->getChild( 0 )->right );
		$this->assertEquals( 2, count( $testroot->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 4, $testroot->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 0 )->getChild( 1 )->level );
		$this->assertEquals( 5, $testroot->getChild( 0 )->getChild( 1 )->left );
		$this->assertEquals( 6, $testroot->getChild( 0 )->getChild( 1 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 0 )->getChild( 1 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 1 )->level );
		$this->assertEquals( 8, $testroot->getChild( 1 )->left );
		$this->assertEquals( 15, $testroot->getChild( 1 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 1 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 1 )->getChild( 0 )->level );
		$this->assertEquals( 9, $testroot->getChild( 1 )->getChild( 0 )->left );
		$this->assertEquals( 14, $testroot->getChild( 1 )->getChild( 0 )->right );
		$this->assertEquals( 2, count( $testroot->getChild( 1 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 1 )->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 10, $testroot->getChild( 1 )->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 11, $testroot->getChild( 1 )->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 1 )->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 1 )->getChild( 0 )->getChild( 1 )->level );
		$this->assertEquals( 12, $testroot->getChild( 1 )->getChild( 0 )->getChild( 1 )->left );
		$this->assertEquals( 13, $testroot->getChild( 1 )->getChild( 0 )->getChild( 1 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 1 )->getChild( 0 )->getChild( 1 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 2 )->level );
		$this->assertEquals( 16, $testroot->getChild( 2 )->left );
		$this->assertEquals( 17, $testroot->getChild( 2 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 2 )->getChildren() ) );
	}


	public function testMoveNode2()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getChild( 0 )->getId();
		$oldparentid = $root->getChild( 1 )->getId();
		$newparentid = $root->getId();
		$refnodeid = $root->getChild( 1 )->getId();

		$manager->moveNode( (string) $nodeid, $oldparentid, $newparentid, $refnodeid );

		$testroot = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$this->assertEquals( 0, $testroot->level );
		$this->assertEquals( 1, $testroot->left );
		$this->assertEquals( 18, $testroot->right );
		$this->assertEquals( 4, count( $testroot->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 0 )->level );
		$this->assertEquals( 2, $testroot->getChild( 0 )->left );
		$this->assertEquals( 7, $testroot->getChild( 0 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 6, $testroot->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 4, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 5, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 1 )->level );
		$this->assertEquals( 8, $testroot->getChild( 1 )->left );
		$this->assertEquals( 13, $testroot->getChild( 1 )->right );
		$this->assertEquals( 2, count( $testroot->getChild( 1 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 1 )->getChild( 0 )->level );
		$this->assertEquals( 9, $testroot->getChild( 1 )->getChild( 0 )->left );
		$this->assertEquals( 10, $testroot->getChild( 1 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 1 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 1 )->getChild( 1 )->level );
		$this->assertEquals( 11, $testroot->getChild( 1 )->getChild( 1 )->left );
		$this->assertEquals( 12, $testroot->getChild( 1 )->getChild( 1 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 1 )->getChild( 1 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 2 )->level );
		$this->assertEquals( 14, $testroot->getChild( 2 )->left );
		$this->assertEquals( 15, $testroot->getChild( 2 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 2 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 3 )->level );
		$this->assertEquals( 16, $testroot->getChild( 3 )->left );
		$this->assertEquals( 17, $testroot->getChild( 3 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 3 )->getChildren() ) );
	}


	public function testMoveNode3()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getChild( 0 )->getId();
		$oldparentid = $root->getChild( 1 )->getId();
		$newparentid = $root->getId();
		$refnodeid = $root->getChild( 2 )->getId();

		$manager->moveNode( (string) $nodeid, $oldparentid, $newparentid, $refnodeid );

		$testroot = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$this->assertEquals( 0, $testroot->level );
		$this->assertEquals( 1, $testroot->left );
		$this->assertEquals( 18, $testroot->right );
		$this->assertEquals( 4, count( $testroot->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 0 )->level );
		$this->assertEquals( 2, $testroot->getChild( 0 )->left );
		$this->assertEquals( 7, $testroot->getChild( 0 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 6, $testroot->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 4, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 5, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 1 )->level );
		$this->assertEquals( 8, $testroot->getChild( 1 )->left );
		$this->assertEquals( 9, $testroot->getChild( 1 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 1 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 2 )->level );
		$this->assertEquals( 10, $testroot->getChild( 2 )->left );
		$this->assertEquals( 15, $testroot->getChild( 2 )->right );
		$this->assertEquals( 2, count( $testroot->getChild( 2 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 2 )->getChild( 0 )->level );
		$this->assertEquals( 11, $testroot->getChild( 2 )->getChild( 0 )->left );
		$this->assertEquals( 12, $testroot->getChild( 2 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 2 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 2 )->getChild( 1 )->level );
		$this->assertEquals( 13, $testroot->getChild( 2 )->getChild( 1 )->left );
		$this->assertEquals( 14, $testroot->getChild( 2 )->getChild( 1 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 2 )->getChild( 1 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 3 )->level );
		$this->assertEquals( 16, $testroot->getChild( 3 )->left );
		$this->assertEquals( 17, $testroot->getChild( 3 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 3 )->getChildren() ) );
	}


	public function testMoveNode4()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getChild( 0 )->getId();
		$oldparentid = $root->getChild( 1 )->getId();
		$newparentid = $root->getId();
		$refnodeid = null;

		$manager->moveNode( (string) $nodeid, $oldparentid, $newparentid, $refnodeid );

		$testroot = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$this->assertEquals( 0, $testroot->level );
		$this->assertEquals( 1, $testroot->left );
		$this->assertEquals( 18, $testroot->right );
		$this->assertEquals( 4, count( $testroot->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 0 )->level );
		$this->assertEquals( 2, $testroot->getChild( 0 )->left );
		$this->assertEquals( 7, $testroot->getChild( 0 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 6, $testroot->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 4, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 5, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 1 )->level );
		$this->assertEquals( 8, $testroot->getChild( 1 )->left );
		$this->assertEquals( 9, $testroot->getChild( 1 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 1 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 2 )->level );
		$this->assertEquals( 10, $testroot->getChild( 2 )->left );
		$this->assertEquals( 11, $testroot->getChild( 2 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 2 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 3 )->level );
		$this->assertEquals( 12, $testroot->getChild( 3 )->left );
		$this->assertEquals( 17, $testroot->getChild( 3 )->right );
		$this->assertEquals( 2, count( $testroot->getChild( 3 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 3 )->getChild( 0 )->level );
		$this->assertEquals( 13, $testroot->getChild( 3 )->getChild( 0 )->left );
		$this->assertEquals( 14, $testroot->getChild( 3 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 3 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 3 )->getChild( 1 )->level );
		$this->assertEquals( 15, $testroot->getChild( 3 )->getChild( 1 )->left );
		$this->assertEquals( 16, $testroot->getChild( 3 )->getChild( 1 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 3 )->getChild( 1 )->getChildren() ) );
	}


	public function testMoveNode5()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getChild( 0 )->getId();
		$oldparentid = $root->getChild( 1 )->getId();
		$newparentid = $root->getChild( 2 )->getId();
		$refnodeid = null;

		$manager->moveNode( (string) $nodeid, $oldparentid, $newparentid, $refnodeid );

		$testroot = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$this->assertEquals( 0, $testroot->level );
		$this->assertEquals( 1, $testroot->left );
		$this->assertEquals( 18, $testroot->right );
		$this->assertEquals( 3, count( $testroot->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 0 )->level );
		$this->assertEquals( 2, $testroot->getChild( 0 )->left );
		$this->assertEquals( 7, $testroot->getChild( 0 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 6, $testroot->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 4, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 5, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 1 )->level );
		$this->assertEquals( 8, $testroot->getChild( 1 )->left );
		$this->assertEquals( 9, $testroot->getChild( 1 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 1 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 2 )->level );
		$this->assertEquals( 10, $testroot->getChild( 2 )->left );
		$this->assertEquals( 17, $testroot->getChild( 2 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 2 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 2 )->getChild( 0 )->level );
		$this->assertEquals( 11, $testroot->getChild( 2 )->getChild( 0 )->left );
		$this->assertEquals( 16, $testroot->getChild( 2 )->getChild( 0 )->right );
		$this->assertEquals( 2, count( $testroot->getChild( 2 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 2 )->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 12, $testroot->getChild( 2 )->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 13, $testroot->getChild( 2 )->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 2 )->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 2 )->getChild( 0 )->getChild( 1 )->level );
		$this->assertEquals( 14, $testroot->getChild( 2 )->getChild( 0 )->getChild( 1 )->left );
		$this->assertEquals( 15, $testroot->getChild( 2 )->getChild( 0 )->getChild( 1 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 2 )->getChild( 0 )->getChild( 1 )->getChildren() ) );
	}


	public function testMoveNode6()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getChild( 0 )->getId();
		$oldparentid = $root->getChild( 1 )->getId();
		$newparentid = $root->getId();
		$refnodeid = $root->getChild( 2 )->getId();

		$manager->moveNode( (string) $nodeid, $oldparentid, $newparentid, $refnodeid );


		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$nodeid = $root->getChild( 2 )->getId();
		$oldparentid = $root->getId();
		$newparentid = $root->getChild( 1 )->getId();
		$refnodeid = null;

		$manager->moveNode( $nodeid, $oldparentid, $newparentid, $refnodeid );


		$testroot = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$this->assertEquals( 0, $testroot->level );
		$this->assertEquals( 1, $testroot->left );
		$this->assertEquals( 18, $testroot->right );
		$this->assertEquals( 3, count( $testroot->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 0 )->level );
		$this->assertEquals( 2, $testroot->getChild( 0 )->left );
		$this->assertEquals( 7, $testroot->getChild( 0 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 6, $testroot->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 4, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 5, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 1 )->level );
		$this->assertEquals( 8, $testroot->getChild( 1 )->left );
		$this->assertEquals( 15, $testroot->getChild( 1 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 1 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 1 )->getChild( 0 )->level );
		$this->assertEquals( 9, $testroot->getChild( 1 )->getChild( 0 )->left );
		$this->assertEquals( 14, $testroot->getChild( 1 )->getChild( 0 )->right );
		$this->assertEquals( 2, count( $testroot->getChild( 1 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 1 )->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 10, $testroot->getChild( 1 )->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 11, $testroot->getChild( 1 )->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 1 )->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 1 )->getChild( 0 )->getChild( 1 )->level );
		$this->assertEquals( 12, $testroot->getChild( 1 )->getChild( 0 )->getChild( 1 )->left );
		$this->assertEquals( 13, $testroot->getChild( 1 )->getChild( 0 )->getChild( 1 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 1 )->getChild( 0 )->getChild( 1 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 2 )->level );
		$this->assertEquals( 16, $testroot->getChild( 2 )->left );
		$this->assertEquals( 17, $testroot->getChild( 2 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 2 )->getChildren() ) );
	}


	public function testMoveNode7()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getChild( 0 )->getId();
		$oldparentid = $root->getChild( 1 )->getId();
		$newparentid = $root->getId();
		$refnodeid = $root->getChild( 1 )->getId();

		$manager->moveNode( (string) $nodeid, $oldparentid, $newparentid, $refnodeid );


		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getId();
		$oldparentid = $root->getId();
		$newparentid = $root->getChild( 2 )->getId();
		$refnodeid = null;

		$manager->moveNode( $nodeid, $oldparentid, $newparentid, $refnodeid );


		$testroot = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$this->assertEquals( 0, $testroot->level );
		$this->assertEquals( 1, $testroot->left );
		$this->assertEquals( 18, $testroot->right );
		$this->assertEquals( 3, count( $testroot->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 0 )->level );
		$this->assertEquals( 2, $testroot->getChild( 0 )->left );
		$this->assertEquals( 7, $testroot->getChild( 0 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 6, $testroot->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 4, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 5, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 1 )->level );
		$this->assertEquals( 8, $testroot->getChild( 1 )->left );
		$this->assertEquals( 15, $testroot->getChild( 1 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 1 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 1 )->getChild( 0 )->level );
		$this->assertEquals( 9, $testroot->getChild( 1 )->getChild( 0 )->left );
		$this->assertEquals( 14, $testroot->getChild( 1 )->getChild( 0 )->right );
		$this->assertEquals( 2, count( $testroot->getChild( 1 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 1 )->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 10, $testroot->getChild( 1 )->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 11, $testroot->getChild( 1 )->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 1 )->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 1 )->getChild( 0 )->getChild( 1 )->level );
		$this->assertEquals( 12, $testroot->getChild( 1 )->getChild( 0 )->getChild( 1 )->left );
		$this->assertEquals( 13, $testroot->getChild( 1 )->getChild( 0 )->getChild( 1 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 1 )->getChild( 0 )->getChild( 1 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 2 )->level );
		$this->assertEquals( 16, $testroot->getChild( 2 )->left );
		$this->assertEquals( 17, $testroot->getChild( 2 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 2 )->getChildren() ) );
	}


	public function testMoveNode8()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getId();
		$oldparentid = $root->getId();
		$newparentid = $root->getChild( 0 )->getId();
		$refnodeid = null;

		$manager->moveNode( (string) $nodeid, $oldparentid, $newparentid, $refnodeid );


		$testroot = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$this->assertEquals( 0, $testroot->level );
		$this->assertEquals( 1, $testroot->left );
		$this->assertEquals( 18, $testroot->right );
		$this->assertEquals( 2, count( $testroot->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 0 )->level );
		$this->assertEquals( 2, $testroot->getChild( 0 )->left );
		$this->assertEquals( 15, $testroot->getChild( 0 )->right );
		$this->assertEquals( 2, count( $testroot->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 6, $testroot->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 4, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 5, $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 0 )->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 2, $testroot->getChild( 0 )->getChild( 1 )->level );
		$this->assertEquals( 7, $testroot->getChild( 0 )->getChild( 1 )->left );
		$this->assertEquals( 14, $testroot->getChild( 0 )->getChild( 1 )->right );
		$this->assertEquals( 1, count( $testroot->getChild( 0 )->getChild( 1 )->getChildren() ) );

		$this->assertEquals( 3, $testroot->getChild( 0 )->getChild( 1 )->getChild( 0 )->level );
		$this->assertEquals( 8, $testroot->getChild( 0 )->getChild( 1 )->getChild( 0 )->left );
		$this->assertEquals( 13, $testroot->getChild( 0 )->getChild( 1 )->getChild( 0 )->right );
		$this->assertEquals( 2, count( $testroot->getChild( 0 )->getChild( 1 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 4, $testroot->getChild( 0 )->getChild( 1 )->getChild( 0 )->getChild( 0 )->level );
		$this->assertEquals( 9, $testroot->getChild( 0 )->getChild( 1 )->getChild( 0 )->getChild( 0 )->left );
		$this->assertEquals( 10, $testroot->getChild( 0 )->getChild( 1 )->getChild( 0 )->getChild( 0 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 0 )->getChild( 1 )->getChild( 0 )->getChild( 0 )->getChildren() ) );

		$this->assertEquals( 4, $testroot->getChild( 0 )->getChild( 1 )->getChild( 0 )->getChild( 1 )->level );
		$this->assertEquals( 11, $testroot->getChild( 0 )->getChild( 1 )->getChild( 0 )->getChild( 1 )->left );
		$this->assertEquals( 12, $testroot->getChild( 0 )->getChild( 1 )->getChild( 0 )->getChild( 1 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 0 )->getChild( 1 )->getChild( 0 )->getChild( 1 )->getChildren() ) );

		$this->assertEquals( 1, $testroot->getChild( 1 )->level );
		$this->assertEquals( 16, $testroot->getChild( 1 )->left );
		$this->assertEquals( 17, $testroot->getChild( 1 )->right );
		$this->assertEquals( 0, count( $testroot->getChild( 1 )->getChildren() ) );
	}


	public function testMoveNodeException()
	{
		$this->config['sql']['move-left'] = '
			UPDATE "mw_tree_test"
			SET nleft123 = nleft + ?, level = level + ?
			WHERE nleft >= ? AND nleft <= ?
		';

		$this->config['sql']['move-right'] = '
			UPDATE "mw_tree_test"
			SET nright123 = nright + ?
			WHERE nright >= ? AND nright <= ?
		';


		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );

		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getId();
		$oldparentid = $root->getId();
		$newparentid = $root->getChild( 0 )->getId();

		$this->expectException( \Aimeos\MW\DB\Exception::class );
		$manager->moveNode( (string) $nodeid, $oldparentid, $newparentid );
	}


	public function testSaveNode()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );

		$root->setLabel( 'rooot' );
		$result = $manager->saveNode( $root );

		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );
		$this->assertInstanceOf( \Aimeos\MW\Tree\Node\Iface::class, $result );
		$this->assertEquals( 'rooot', $root->getLabel() );
	}


	public function testSaveNodeException()
	{
		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$node = $manager->createNode();

		$this->expectException( \Aimeos\MW\Tree\Exception::class );
		$manager->saveNode( $node );
	}


	public function testSaveNodeException2()
	{
		$this->config['sql']['update'] = '
			UPDATE "mw_tree_test" SET label123 = ?, status = ? WHERE id = ?
		';

		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );
		$root = $manager->getNode( null, \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );

		$root->setLabel( 'rooot' );

		$this->expectException( \Aimeos\MW\DB\Exception::class );
		$manager->saveNode( $root );
	}


	public function testSetReadOnly()
	{
		$class = new \ReflectionClass( \Aimeos\MW\Tree\Manager\DBNestedSet::class );
		$method = $class->getMethod( 'setReadOnly' );
		$method->setAccessible( true );

		$manager = new \Aimeos\MW\Tree\Manager\DBNestedSet( $this->config, self::$dbm );

		$method->invokeArgs( $manager, [] );

		$this->assertTrue( $manager->isReadOnly() );
	}

}
