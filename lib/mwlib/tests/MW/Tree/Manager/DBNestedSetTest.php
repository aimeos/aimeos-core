<?php

/**
 * Test class for MW_Tree_Manager_DBNestedSet.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Tree_Manager_DBNestedSetTest extends MW_Unittest_Testcase
{
	private $_dbm;
	private $_config;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		if( TestHelper::getConfig()->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$this->_config = array();

		$this->_config['search'] = array(
			'id' => array( 'label' => 'Tree node ID', 'code' => 'tree.id', 'internalcode' => 'id', 'type' => 'integer', 'internaltype' => MW_DB_Statement_Abstract::PARAM_INT ),
			'parentid' => array( 'label' => 'Tree node parent id', 'code' => 'tree.parentid', 'internalcode' => 'parentid', 'type' => 'integer', 'internaltype' => MW_DB_Statement_Abstract::PARAM_INT ),
			'label' => array( 'label' => 'Tree node name', 'code' => 'tree.label', 'internalcode' => 'label', 'type' => 'string', 'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
			'code' => array( 'label' => 'Tree node code', 'code' => 'tree.code', 'internalcode' => 'code', 'type' => 'string', 'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
			'status' => array( 'label' => 'Tree node status', 'code' => 'tree.status', 'internalcode' => 'status', 'type' => 'boolean', 'internaltype' => MW_DB_Statement_Abstract::PARAM_BOOL ),
			'level' => array( 'label' => 'Tree node level', 'code' => 'tree.level', 'internalcode' => 'level', 'type' => 'integer', 'internaltype' => MW_DB_Statement_Abstract::PARAM_INT ),
			'left' => array( 'label' => 'Tree node left number', 'code' => 'tree.left', 'internalcode' => 'nleft', 'type' => 'integer', 'internaltype' => MW_DB_Statement_Abstract::PARAM_INT ),
			'right' => array( 'label' => 'Tree node right number', 'code' => 'tree.right', 'internalcode' => 'nright', 'type' => 'integer', 'internaltype' => MW_DB_Statement_Abstract::PARAM_INT ),

		);

		$this->_config['sql'] = array(
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
			'newid' => '
				SELECT LAST_INSERT_ID()
			',
			'transstart' => 'BEGIN',
			'transcommit' => 'COMMIT',
			'transrollback' => 'ROLLBACK',
		);

		$this->_dbm = TestHelper::getDBManager();
		$conn = $this->_dbm->acquire();

		$sql = 'DROP TABLE IF EXISTS "mw_tree_test"';
		$conn->create( $sql )->execute()->finish();

		$sql = '
			CREATE TABLE IF NOT EXISTS "mw_tree_test" (
				"id" INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
				"parentid" INTEGER NOT NULL,
				"label" VARCHAR(16) NOT NULL,
				"code" VARCHAR(32) NOT NULL,
				"level" INTEGER NOT NULL,
				"nleft" INTEGER NOT NULL,
				"nright" INTEGER NOT NULL,
				"status" SMALLINT NOT NULL
			);
		';
		$conn->create( $sql )->execute()->finish();

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

		$this->_dbm->release( $conn );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$sql = 'DROP TABLE "mw_tree_test"';

		$this->_dbm = TestHelper::getDBManager();
		$conn = $this->_dbm->acquire();
		$conn->create( $sql )->execute()->finish();
		$this->_dbm->release( $conn );
	}


	public function testGetSearchAttributes()
	{
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );

		foreach( $manager->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}


	public function testIsReadOnly()
	{
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );

		$manager->isReadOnly();
	}


	public function testCreateSearch()
	{
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $manager->createSearch() );
	}


	public function testSearchNodes()
	{
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );
		$search = $manager->createSearch();


		$search->setConditions( $search->compare( '==', 'tree.level', 1 ) );
		$nodes = $manager->searchNodes( $search );

		$this->assertEquals( 3, count( $nodes ) );

		foreach( $nodes as $node ) {
			$this->assertInstanceOf( 'MW_Tree_Node_Interface', $node );
		}


		if( ( $node = reset( $nodes ) ) === false ) {
			throw new Exception('No node found');
		}

		$search->setConditions( $search->compare( '==', 'tree.level', 3 ) );
		$nodes = $manager->searchNodes( $search, $node->getId() );

		$this->assertEquals( 1, count( $nodes ) );

		foreach( $nodes as $node ) {
			$this->assertInstanceOf( 'MW_Tree_Node_Interface', $node );
		}
	}


	public function testSearchException()
	{
		$this->_config['sql']['search'] = '
			SELECT "id", "label", "code", "status", "level", "domain" as "base", "left" AS "left", "right" AS "right"
			FROM "mw_tree_test"
			WHERE domain123 = ? AND nleft >= ? AND nright <= ? AND :cond
		';

		$this->setExpectedException( 'MW_DB_Exception' );

		$manager = new MW_Tree_Manager_DBNestedSet($this->_config, $this->_dbm);
		$manager->searchNodes( $manager->createSearch() );
	}


	public function testDeleteNode()
	{
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'tree.label', 'l2n2' ) );
		$nodes = $manager->searchNodes( $search );
		$this->assertEquals( 1, count( $nodes ) );

		$manager->deleteNode( reset( $nodes )->getId() );

		$search = $manager->createSearch();
		$nodes = $manager->searchNodes( $search );
		$this->assertEquals( 7, count( $nodes ) );

		$manager->deleteNode();

		$search = $manager->createSearch();
		$nodes = $manager->searchNodes( $search );
		$this->assertEquals( 1, count( $nodes ) );
	}


	public function testDeleteNodeException()
	{
		$this->_config['sql']['search'] = '
			DELETE FROM "mw_tree_test" WHERE domain = ? AND nleft12 >= ? AND nright <= ?
		';

		$this->setExpectedException( 'MW_DB_Exception' );

		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );
		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_ONE  );
		$manager->deleteNode($root->getId());
	}


	public function testGetNode()
	{
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );

		$node = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_ONE );
		$this->assertEquals( 0, $node->level );
		$this->assertEquals( 0, count( $node->getChildren() ) );

		$node = $manager->getNode( $node->getId(), MW_Tree_Manager_Abstract::LEVEL_LIST );
		$this->assertEquals( 3, count( $node->getChildren() ) );
		$this->assertEquals( 0, count( $node->getChild( 0 )->getChildren() ) );
		$this->assertEquals( 0, count( $node->getChild( 1 )->getChildren() ) );

		$node = $manager->getNode( $node->getId(), MW_Tree_Manager_Abstract::LEVEL_TREE );
		$this->assertEquals( 3, count( $node->getChildren() ) );
		$this->assertEquals( 1, count( $node->getChild( 0 )->getChildren() ) );
		$this->assertEquals( 1, count( $node->getChild( 0 )->getChild( 0 )->getChildren() ) );
		$this->assertEquals( 1, count( $node->getChild( 1 )->getChildren() ) );
		$this->assertEquals( 2, count( $node->getChild( 1 )->getChild( 0 )->getChildren() ) );
		$this->assertEquals( 0, count( $node->getChild( 2 )->getChildren() ) );
	}


	public function testGetPath()
	{
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );

		$nodes = array();
		$nodes[0] = $manager->getNode();
		$nodes[1] = $nodes[0]->getChild( 1 );
		$nodes[2] = $nodes[1]->getChild( 0 );
		$nodes[3] = $nodes[2]->getChild( 1 );

		$path = $manager->getPath( $nodes[3]->getId() );

		foreach( $nodes as $node )
		{
			if( ( $actual = array_shift( $path ) ) === null ) {
				throw new Exception( 'Not enough nodes in path' );
			}

			$this->assertEquals( $node->getId(), $actual->getId() );
		}
	}


	public function testGetLevelFromConstantException()
	{
		$manager = new MW_Tree_Manager_DBNestedSet($this->_config, $this->_dbm);

		$this->setExpectedException('MW_Tree_Exception');
		$manager->getNode( null, 0);
	}


	public function testInsertNode()
	{
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );
		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_ONE );

		$newNode = $manager->createNode();
		$newNode->setLabel( 'l1n4' );
		$manager->insertNode( $newNode, $root->getId() );

		$root = $manager->getNode( $root->getId(), MW_Tree_Manager_Abstract::LEVEL_LIST );
		$this->assertEquals( 4, count( $root->getChildren() ) );
		$this->assertEquals( 'l1n4', $root->getChild( 3 )->getLabel() );
		$this->assertEquals( $newNode->getId(), $root->getChild( 3 )->getId() );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'tree.label', 'l1n3' ) );
		$nodes = $manager->searchNodes( $search );
		$this->assertEquals( 1, count( $nodes ) );

		$newNode->setLabel( 'new l1n3' );
		$manager->insertNode( $newNode, $root->getId(), reset( $nodes )->getId() );

		$root = $manager->getNode( $root->getId(), MW_Tree_Manager_Abstract::LEVEL_LIST );
		$this->assertEquals( 5, count( $root->getChildren() ) );
		$this->assertEquals( 'l1n2', $root->getChild( 1 )->getLabel() );
		$this->assertEquals( 'new l1n3', $root->getChild( 2 )->getLabel() );
		$this->assertEquals( 'l1n3', $root->getChild( 3 )->getLabel() );
		$this->assertEquals( 'l1n4', $root->getChild( 4 )->getLabel() );
	}


	public function testInsertNodeException()
	{
		$manager = new MW_Tree_Manager_DBNestedSet($this->_config, $this->_dbm);
		$newNode = $manager->createNode();

		$this->setExpectedException( 'MW_Tree_Exception' );
		$manager->insertNode( $newNode, -1 );
	}


	public function testInsertNodeRoot()
	{
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );

		$newNode = $manager->createNode();
		$newNode->setCode( 'root3' );
		$newNode->setLabel( 'Root 3' );

		$manager->insertNode( $newNode );

		$root = $manager->getNode( $newNode->getId() );
		$this->assertEquals( 'Root 3', $root->getLabel() );
		$this->assertEquals( 21, $root->left );
		$this->assertEquals( 22, $root->right );
	}


	public function testMoveNode1()
	{
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );
		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

		$nodeid = $root->getChild( 0 )->getChild( 0 )->getChild( 0 )->getId();
		$oldparentid = $root->getChild( 0 )->getChild( 0 )->getId();
		$newparentid = $root->getChild( 0 )->getId();
		$refnodeid = null;

		$manager->moveNode( $nodeid, $oldparentid, $newparentid, $refnodeid );

		$testroot = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

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
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );
		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getChild( 0 )->getId();
		$oldparentid = $root->getChild( 1 )->getId();
		$newparentid = $root->getId();
		$refnodeid = $root->getChild( 1 )->getId();

		$manager->moveNode( $nodeid, $oldparentid, $newparentid, $refnodeid );

		$testroot = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

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
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );
		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getChild( 0 )->getId();
		$oldparentid = $root->getChild( 1 )->getId();
		$newparentid = $root->getId();
		$refnodeid = $root->getChild( 2 )->getId();

		$manager->moveNode( $nodeid, $oldparentid, $newparentid, $refnodeid );

		$testroot = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

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
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );
		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getChild( 0 )->getId();
		$oldparentid = $root->getChild( 1 )->getId();
		$newparentid = $root->getId();
		$refnodeid = null;

		$manager->moveNode( $nodeid, $oldparentid, $newparentid, $refnodeid );

		$testroot = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

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
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );
		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getChild( 0 )->getId();
		$oldparentid = $root->getChild( 1 )->getId();
		$newparentid = $root->getChild( 2 )->getId();
		$refnodeid = null;

		$manager->moveNode( $nodeid, $oldparentid, $newparentid, $refnodeid );

		$testroot = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

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
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );
		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getChild( 0 )->getId();
		$oldparentid = $root->getChild( 1 )->getId();
		$newparentid = $root->getId();
		$refnodeid = $root->getChild( 2 )->getId();

		$manager->moveNode( $nodeid, $oldparentid, $newparentid, $refnodeid );


		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

		$nodeid = $root->getChild( 2 )->getId();
		$oldparentid = $root->getId();
		$newparentid = $root->getChild( 1 )->getId();
		$refnodeid = null;

		$manager->moveNode( $nodeid, $oldparentid, $newparentid, $refnodeid );


		$testroot = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

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
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );
		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getChild( 0 )->getId();
		$oldparentid = $root->getChild( 1 )->getId();
		$newparentid = $root->getId();
		$refnodeid = $root->getChild( 1 )->getId();

		$manager->moveNode( $nodeid, $oldparentid, $newparentid, $refnodeid );


		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getId();
		$oldparentid = $root->getId();
		$newparentid = $root->getChild( 2 )->getId();
		$refnodeid = null;

		$manager->moveNode( $nodeid, $oldparentid, $newparentid, $refnodeid );


		$testroot = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

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
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );
		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getId();
		$oldparentid = $root->getId();
		$newparentid = $root->getChild( 0 )->getId();
		$refnodeid = null;

		$manager->moveNode( $nodeid, $oldparentid, $newparentid, $refnodeid );


		$testroot = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

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
		$this->_config['sql']['move-left'] = '
			UPDATE "mw_tree_test"
			SET nleft123 = nleft + ?, level = level + ?
			WHERE nleft >= ? AND nleft <= ?
		';

		$this->_config['sql']['move-right'] = '
			UPDATE "mw_tree_test"
			SET nright123 = nright + ?
			WHERE nright >= ? AND nright <= ?
		';


		$manager = new MW_Tree_Manager_DBNestedSet($this->_config, $this->_dbm);

		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_TREE );

		$nodeid = $root->getChild( 1 )->getId();
		$oldparentid = $root->getId();
		$newparentid = $root->getChild( 0 )->getId();

		$this->setExpectedException( 'MW_DB_Exception' );
		$manager->moveNode( $nodeid, $oldparentid, $newparentid );
	}


	public function testSaveNode()
	{
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );
		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_ONE );

		$root->setLabel( 'rooot' );
		$manager->saveNode( $root );

		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_ONE );
		$this->assertEquals( 'rooot', $root->getLabel() );
	}


	public function testSaveNodeException()
	{
		$manager = new MW_Tree_Manager_DBNestedSet( $this->_config, $this->_dbm );
		$node = $manager->createNode();

		$this->setExpectedException( 'MW_Tree_Exception' );
		$manager->saveNode($node);
	}


	public function testSaveNodeException2()
	{
		$this->_config['sql']['update'] = '
			UPDATE "mw_tree_test" SET label123 = ?, status = ? WHERE id = ?
		';

		$manager = new MW_Tree_Manager_DBNestedSet($this->_config, $this->_dbm);
		$root = $manager->getNode( null, MW_Tree_Manager_Abstract::LEVEL_ONE );

		$root->setLabel( 'rooot' );

		$this->setExpectedException( 'MW_DB_Exception' );
		$manager->saveNode( $root );
	}


	public function testConstructor()
	{
		$this->setExpectedException( 'MW_Tree_Exception' );
		$obj = new MW_Tree_Manager_DBNestedSet($this->_config, null);
	}


	public function testConstructor2()
	{
		$this->setExpectedException( 'MW_Tree_Exception' );
		$obj = new MW_Tree_Manager_DBNestedSet(array(), $this->_dbm);
	}

}
