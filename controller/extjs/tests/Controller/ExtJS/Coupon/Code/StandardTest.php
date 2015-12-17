<?php

namespace Aimeos\Controller\ExtJS\Coupon\Code;


/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;
	private $testdir;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = \TestHelperExtjs::getContext();

		$this->testdir = $this->context->getConfig()->get( 'controller/extjs/attribute/import/text/standard/uploaddir', './tmp' );

		if( !is_dir( $this->testdir ) && mkdir( $this->testdir, 0775, true ) === false ) {
			throw new \Exception( sprintf( 'Unable to create missing upload directory "%1$s"', $this->testdir ) );
		}

		$this->object = new \Aimeos\Controller\ExtJS\Coupon\Code\Standard( $this->context );
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
			'condition' => (object) array(
				'&&' => array(
					0 => array( '~=' => (object) array( 'coupon.code.code' => 'OPQR' ) ),
					1 => array( '==' => (object) array( 'coupon.code.editor' => 'core:unittest' ) )
				)
			),
			'sort' => 'coupon.code.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( '2000000', $result['items'][0]->{'coupon.code.count'} );
	}


	public function testSaveDeleteItem()
	{
		$couponManager = \Aimeos\MShop\Coupon\Manager\Factory::createManager( \TestHelperExtjs::getContext() );
		$search = $couponManager->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.label', 'Unit test example' ) );
		$result = $couponManager->searchItems( $search );

		if( ( $couponItem = reset( $result ) ) === false ) {
			throw new \Exception( 'No coupon item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'coupon.code.parentid' => $couponItem->getId(),
				'coupon.code.code' => 'zzzz',
				'coupon.code.count' => -1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'coupon.code.code' => 'zzzz' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => array( $saved['items']->{'coupon.code.id'}) );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'coupon.code.id'} );
		$this->assertEquals( $saved['items']->{'coupon.code.parentid'}, $searched['items'][0]->{'coupon.code.parentid'} );
		$this->assertEquals( $saved['items']->{'coupon.code.code'}, $searched['items'][0]->{'coupon.code.code'} );
		$this->assertEquals( $saved['items']->{'coupon.code.count'}, $searched['items'][0]->{'coupon.code.count'} );
		$this->assertEquals( $saved['items']->{'coupon.code.datestart'}, $searched['items'][0]->{'coupon.code.datestart'} );
		$this->assertEquals( $saved['items']->{'coupon.code.dateend'}, $searched['items'][0]->{'coupon.code.dateend'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testAbstractInit()
	{
		$expected = array( 'success' => true );
		$actual = $this->object->init( new \stdClass() );
		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractFinish()
	{
		$expected = array( 'success' => true );
		$actual = $this->object->finish( new \stdClass() );
		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractGetItemSchema()
	{
		$actual = $this->object->getItemSchema();
		$expected = array(
			'name' => 'Coupon_Code',
			'properties' => array(
				'coupon.code.id' => array(
					'description' => 'Coupon code ID',
					'optional' => false,
					'type' => 'integer',
				),
				'coupon.code.siteid' => array(
					'description' => 'Coupon code site ID',
					'optional' => false,
					'type' => 'integer',
				),
				'coupon.code.parentid' => array(
					'description' => 'Coupon ID',
					'optional' => false,
					'type' => 'integer',
				),
				'coupon.code.code' => array(
					'description' => 'Coupon code value',
					'optional' => false,
					'type' => 'string',
				),
				'coupon.code.count' => array(
					'description' => 'Coupon code quantity',
					'optional' => false,
					'type' => 'string',
				),
				'coupon.code.datestart' => array(
					'description' => 'Coupon code start date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.dateend' => array(
					'description' => 'Coupon code end date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.ctime' => array(
					'description' => 'Coupon code create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.mtime' => array(
					'description' => 'Coupon code modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.editor' => array(
					'description' => 'Coupon code editor',
					'optional' => false,
					'type' => 'string',
				),
			)
		);

		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractGetSearchSchema()
	{
		$actual = $this->object->getSearchSchema();
		$expected = array(
			'criteria' => array(
				'coupon.code.code' => array(
					'description' => 'Coupon code value',
					'optional' => false,
					'type' => 'string',
				),
				'coupon.code.count' => array(
					'description' => 'Coupon code quantity',
					'optional' => false,
					'type' => 'string',
				),
				'coupon.code.datestart' => array(
					'description' => 'Coupon code start date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.dateend' => array(
					'description' => 'Coupon code end date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.ctime' => array(
					'description' => 'Coupon code create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.mtime' => array(
					'description' => 'Coupon code modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.editor' => array(
					'description' => 'Coupon code editor',
					'optional' => false,
					'type' => 'string',
				),
			)
		);

		$this->assertEquals( $expected, $actual );
	}


	public function testGetServiceDescription()
	{
		$actual = $this->object->getServiceDescription();

		$this->assertArrayHasKey( 'Coupon_Code.uploadFile', $actual );
		$this->assertArrayHasKey( 'Coupon_Code.importFile', $actual );
	}


	public function testUploadFile()
	{
		$config = $this->context->getConfig();
		$config->set( 'controller/extjs/coupon/code/standard/uploaddir', './tmp' );
		$config->set( 'controller/extjs/coupon/code/standard/enablecheck', false );

		$cntlMock = $this->getMockBuilder( '\\Aimeos\\Controller\\ExtJS\\Admin\\Job\\Standard' )
			->setMethods( array( 'saveItems' ) )->setConstructorArgs( array( $this->context ) )->getMock();

		$cntlMock->expects( $this->once() )->method( 'saveItems' );


		$name = 'ControllerExtJSCouponCodeDefaultRun';
		$this->context->getConfig()->set( 'controller/extjs/admin/job/name', $name );

		\Aimeos\Controller\ExtJS\Admin\Job\Factory::injectController( '\\Aimeos\\Controller\\ExtJS\\Admin\\Job\\' . $name, $cntlMock );


		$testfiledir = __DIR__ . DIRECTORY_SEPARATOR . 'testfiles' . DIRECTORY_SEPARATOR;
		exec( sprintf( 'cp -r %1$s %2$s', escapeshellarg( $testfiledir ) . '*', escapeshellarg( $this->testdir ) ) );

		$_FILES['unittest'] = array(
			'name' => 'coupon.zip',
			'tmp_name' => $this->testdir . DIRECTORY_SEPARATOR . 'coupon.zip',
			'error' => UPLOAD_ERR_OK,
		);

		$params = new \stdClass();
		$params->items = $this->testdir . DIRECTORY_SEPARATOR . 'coupon.zip';
		$params->site = $this->context->getLocale()->getSite()->getCode();
		$params->parentid = '-1';


		$result = $this->object->uploadFile( $params );

		$this->assertTrue( file_exists( $result['items'] ) );
		unlink( $result['items'] );
	}


	public function testUploadFileExeptionNoFiles()
	{
		$params = new \stdClass();
		$params->items = basename( $this->testdir . DIRECTORY_SEPARATOR . 'coupon.zip' );
		$params->site = 'unittest';

		$_FILES = array();

		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		$this->object->uploadFile( $params );
	}


	public function testImportFile()
	{
		$codeMock = $this->getMockBuilder( '\\Aimeos\\MShop\\Coupon\\Manager\\Code\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$codeMock->expects( $this->exactly( 3 ) )->method( 'saveItem' );

		$mock = $this->getMockBuilder( '\\Aimeos\\MShop\\Coupon\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'getSubManager' ) )
			->getMock();

		$mock->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $codeMock ) );

		$name = 'ControllerExtJSCouponCodeDefaultRun';
		$this->context->getConfig()->set( 'mshop/coupon/manager/name', $name );

		\Aimeos\MShop\Coupon\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Coupon\\Manager\\' . $name, $mock );


		$testfiledir = __DIR__ . DIRECTORY_SEPARATOR . 'testfiles' . DIRECTORY_SEPARATOR;
		exec( sprintf( 'cp -r %1$s %2$s', escapeshellarg( $testfiledir ) . '*', escapeshellarg( $this->testdir ) ) );


		$params = new \stdClass();
		$params->site = $this->context->getLocale()->getSite()->getCode();
		$params->items = $this->testdir . DIRECTORY_SEPARATOR . 'coupon.zip';
		$params->parentid = '-1';

		$this->object->importFile( $params );
	}
}