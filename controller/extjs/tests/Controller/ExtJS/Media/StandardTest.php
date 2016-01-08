<?php

namespace Aimeos\Controller\ExtJS\Media;


/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;


	protected function setUp()
	{
		$this->context = \TestHelperExtjs::getContext();
		$this->object = new \Aimeos\Controller\ExtJS\Media\Standard( $this->context );
	}


	protected function tearDown()
	{
		unset( $this->context, $this->object );
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'media.label' => 'cn_colombie_' ) ) ) ),
			'sort' => 'media.label',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 6, $result['total'] );
		$this->assertEquals( 'de', $result['items'][0]->{'media.languageid'} );
	}


	public function testSaveDeleteItem()
	{
		$manager = \Aimeos\MShop\Media\Manager\Factory::createManager( \TestHelperExtjs::getContext() );
		$typeManager = $manager->getSubManager( 'type' );
		$criteria = $typeManager->createSearch();
		$criteria->setSlice( 0, 1 );
		$result = $typeManager->searchItems( $criteria );

		if( ( $type = reset( $result ) ) === false ) {
			throw new \Exception( 'No type item found' );
		}

		copy( __DIR__ . '/testfiles/test.png', dirname( dirname( dirname( __DIR__ ) ) ) . '/tmp/test.png' );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'media.label' => 'controller test media',
				'media.domain' => 'attribute',
				'media.typeid' => $type->getId(),
				'media.languageid' => 'de',
				'media.url' => 'test.jpg',
				'media.mimetype' => 'image/jpeg',
				'media.status' => 0,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'media.label' => 'controller test media' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'media.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'media.id'} );
		$this->assertEquals( $saved['items']->{'media.id'}, $searched['items'][0]->{'media.id'} );
		$this->assertEquals( $saved['items']->{'media.typeid'}, $searched['items'][0]->{'media.typeid'} );
		$this->assertEquals( $saved['items']->{'media.domain'}, $searched['items'][0]->{'media.domain'} );
		$this->assertEquals( $saved['items']->{'media.languageid'}, $searched['items'][0]->{'media.languageid'} );
		$this->assertEquals( $saved['items']->{'media.label'}, $searched['items'][0]->{'media.label'} );
		$this->assertEquals( $saved['items']->{'media.url'}, $searched['items'][0]->{'media.url'} );
		$this->assertEquals( $saved['items']->{'media.mimetype'}, $searched['items'][0]->{'media.mimetype'} );
		$this->assertEquals( $saved['items']->{'media.status'}, $searched['items'][0]->{'media.status'} );

		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testGetServiceDescription()
	{
		$expected = array(
			'Media.uploadItem' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "string", "name" => "domain", "optional" => false ),
				),
				"returns" => "array",
			),
			'Media.deleteItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Media.saveItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Media.searchItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "condition", "optional" => true ),
					array( "type" => "integer", "name" => "start", "optional" => true ),
					array( "type" => "integer", "name" => "limit", "optional" => true ),
					array( "type" => "string", "name" => "sort", "optional" => true ),
					array( "type" => "string", "name" => "dir", "optional" => true ),
					array( "type" => "array", "name" => "options", "optional" => true ),
				),
				"returns" => "array",
			),
			'Media.init' => array(
				'parameters' => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				'returns' => 'array',
			),
			'Media.finish' => array(
				'parameters' => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				'returns' => 'array',
			),
		);

		$actual = $this->object->getServiceDescription();

		$this->assertEquals( $expected, $actual );
	}


	public function testFinish()
	{
		$result = $this->object->finish( (object) array( 'site' => 'unittest', 'items' => -1 ) );

		$this->assertEquals( array( 'success' => true ), $result );
	}


	public function testUploadItem()
	{
		$file = array(
			'name' => 'test-binary.bin',
			'tmp_name' => 'test.bin',
			'error' => UPLOAD_ERR_OK,
			'type' => 'application/binary',
			'size' => 1024
		);


		$object = $this->getMockBuilder( '\Aimeos\Controller\ExtJS\Media\Standard' )
			->setMethods( array( 'getUploadedFile' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$name = 'ControllerCommonMediaUploadItem';
		$this->context->getConfig()->set( 'controller/common/media/name', $name );

		$stub = $this->getMockBuilder( '\\Aimeos\\Controller\\Common\\Media\\Standard' )
			->setMethods( array( 'add' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		\Aimeos\Controller\Common\Media\Factory::injectController( '\\Aimeos\\Controller\\Common\\Media\\' . $name, $stub );


		$stub->expects( $this->once() )->method( 'add' );

		$object->expects( $this->once() )->method( 'getUploadedFile' )
			->will( $this->returnValue( $file ) );


		$media = $object->uploadItem( (object) array( 'site' => 'unittest', 'domain' => 'product' ) );

		\Aimeos\Controller\Common\Media\Factory::injectController( '\\Aimeos\\Controller\\Common\\Media\\' . $name, null );

		$this->assertInstanceOf( '\stdClass', $media );
	}


	public function testUploadItemException()
	{
		$this->setExpectedException( '\Aimeos\Controller\ExtJS\Exception' );
		$this->object->uploadItem( (object) array( 'site' => 'unittest', 'domain' => 'product' ) );
	}


	public function testUploadItemExceptionNoUpload()
	{
		$_FILES = array( 'tmp_name' => '/dev/null' );

		$this->setExpectedException( '\Aimeos\Controller\ExtJS\Exception' );
		$this->object->uploadItem( (object) array( 'site' => 'unittest', 'domain' => 'product' ) );
	}
}
