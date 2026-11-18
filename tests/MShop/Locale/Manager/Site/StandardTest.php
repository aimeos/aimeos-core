<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */


namespace Aimeos\MShop\Locale\Manager\Site;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->object = new \Aimeos\MShop\Locale\Manager\Site\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	protected function super() : \Aimeos\MShop\Locale\Manager\Site\Iface
	{
		$context = clone $this->context;
		$view = clone $context->view();
		$view->addHelper( 'access', new \Aimeos\Base\View\Helper\Access\All( $view ) );

		return new \Aimeos\MShop\Locale\Manager\Site\Standard( $context->setView( $view ) );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDelete()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testCreate()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $this->object->create() );
	}


	public function testSaveIdException()
	{
		$this->expectException( \Aimeos\MShop\Locale\Exception::class );
		$this->object->save( $this->object->create() );
	}


	protected function saveConfig( array $stored, array $config ) : array
	{
		$item = $this->super()->insert( $this->object->create()->setCode( 'unittest-config' )->setLabel( 'unittest' )->setConfig( $stored ) );

		try
		{
			$this->object->save( $item->setLabel( 'unittest2' )->setConfig( $config ) );
			return $this->object->get( $item->getId() )->getConfig();
		}
		finally
		{
			$this->object->delete( $item->getId() );
		}
	}


	#[\PHPUnit\Framework\Attributes\DataProvider( 'restrictedConfigProvider' )]
	public function testInsertIgnoresRestrictedConfig( string $key, $value, array $expected )
	{
		$item = $this->object->create()->setCode( 'unittest-insert' )->setLabel( 'unittest' )->setConfig( [$key => $value] );
		$item = $this->object->insert( $item );

		try
		{
			$this->assertEquals( $expected, $this->object->get( $item->getId() )->getConfig() );
		}
		finally
		{
			$this->object->delete( $item->getId() );
		}
	}


	#[\PHPUnit\Framework\Attributes\DataProvider( 'restrictedConfigProvider' )]
	public function testSaveIgnoresRestrictedConfig( string $key, $value, array $expected )
	{
		$this->assertEquals( $expected, $this->saveConfig( [], [$key => $value] ) );
	}


	public static function restrictedConfigProvider() : array
	{
		$result = [];

		foreach( ['resource', 'madmin', 'mshop', 'admin/jqadm/resource', 'controller/frontend'] as $prefix )
		{
			$result[$prefix . ' nested'] = [$prefix, ['manager' => ['name' => 'Custom']], []];
			$result[$prefix . ' flat'] = [$prefix . '/manager/name', 'Custom', []];
			$result[$prefix . ' leading slash'] = ['/' . $prefix . '/manager/name', 'Custom', []];
			$result[$prefix . ' surrounding slashes'] = ['//' . $prefix . '//', ['manager' => ['name' => 'Custom']], []];
			$result[$prefix . ' prefix'] = [$prefix . '-custom', 'value', []];
		}

		$result['resource with email'] = ['resource', ['email' => ['from-email' => 'a@b.c'], 'db' => ['host' => 'x']], ['resource' => ['email' => ['from-email' => 'a@b.c']]]];
		$result['resource email prefix nested'] = ['resource', ['emails' => ['from-email' => 'a@b.c']], []];
		$result['resource email prefix flat'] = ['resource/email-custom', 'value', []];
		$result['resource empty'] = ['resource', [], []];

		return $result;
	}


	public function testAllowsEmailConfig()
	{
		$config = ['resource' => ['email' => ['from-email' => 'a@b.c', 'from-name' => 'Shop']]];
		$this->assertEquals( $config, $this->saveConfig( [], $config ) );
	}


	public function testIgnoresFlatEmailConfig()
	{
		$this->assertEquals( [], $this->saveConfig( [], ['resource/email/from-email' => 'a@b.c'] ) );
	}


	public function testRemovesEmailConfig()
	{
		$config = ['resource' => ['email' => ['from-email' => 'a@b.c']]];
		$this->assertEquals( ['resource' => ['email' => null]], $this->saveConfig( $config, [] ) );
	}


	public function testKeepsStoredConfig()
	{
		$config = ['client' => ['html' => ['x' => 1]], 'resource' => ['email' => ['from-email' => 'a@b.c']]];
		$result = $this->saveConfig( $config, ['resource' => ['email' => ['from-email' => 'b@c.d']]] );

		$this->assertEquals( ['client' => ['html' => ['x' => 1]], 'resource' => ['email' => ['from-email' => 'b@c.d']]], $result );
	}


	public function testKeepsStoredFormConfig()
	{
		$config = ['client' => ['html' => ['a' => null, 'b' => [], 'c' => '1.50', 'd' => 'true', 'e' => ' x', 'f' => '{"g":1}']]];

		// values like the site form submits them after json_decode(), trim() and dropping empty ones
		$form = ['client' => ['html' => ['c' => 1.5, 'd' => true, 'e' => 'x', 'f' => ['g' => 1]]]];

		$this->assertSame( $config, $this->saveConfig( $config, $form ) );
	}


	public function testIgnoresChangedConfig()
	{
		$config = ['client' => ['html' => ['x' => 'admin']]];
		$this->assertSame( $config, $this->saveConfig( $config, ['client' => ['html' => ['x' => true]]] ) );
	}


	public function testIgnoresHiddenConfig()
	{
		$config = ['client' => ['html' => ['x' => 1]]];
		$this->assertEquals( $config, $this->saveConfig( $config, ['client' => ['html' => ['x' => 2]], 'client/html/x' => 1] ) );
	}


	public function testIgnoresJsonEncodedConfig()
	{
		$config = ['mshop' => ['media' => ['manager' => ['extensions' => ['application/pdf' => 'pdf']]]]];
		$result = $this->saveConfig( $config, ['mshop' => ['media' => ['manager' => ['extensions' => '{"application/pdf":"pdf"}']]]] );

		$this->assertSame( $config, $result );
	}


	public function testIgnoresRemovedConfig()
	{
		$config = ['client' => ['html' => ['x' => 1]], 'resource' => ['email' => ['from-email' => 'a@b.c']]];
		$this->assertEquals( $config, $this->saveConfig( $config, ['resource' => ['email' => ['from-email' => 'a@b.c']]] ) );
	}


	public function testAllowsConfiguredConfig()
	{
		$allowed = $this->context->config()->get( 'mshop/locale/manager/site/allowed', [] );
		$this->context->config()->set( 'mshop/locale/manager/site/allowed', $allowed + ['client/html' => 'client/html'] );

		$config = ['client' => ['html' => ['x' => 1]]];
		$this->assertEquals( $config, $this->saveConfig( [], $config ) );
	}


	public function testIgnoresSiteConfiguredConfig()
	{
		$entry = ['client/html' => 'client/html'];
		$allowed = $this->context->config()->get( 'mshop/locale/manager/site/allowed', [] );

		$this->context->config()->set( 'mshop/locale/manager/site/allowed', $allowed + $entry );
		$this->context->locale()->getSiteItem()->setConfigValue( 'mshop/locale/manager/site/allowed', $entry );

		$this->assertEquals( [], $this->saveConfig( [], ['client' => ['html' => ['x' => 1]]] ) );
	}


	public function testSaveUpdateDelete()
	{
		$item = $this->object->create();
		$item->setLabel( 'new name' );
		$item->setStatus( 1 );
		$item->setCode( 'xx' );
		$item->setConfig( ['client' => ['html' => ['catalog' => ['pagesize' => 12]]], 'theme/default/color' => 'blue'] );
		$this->object = $this->super();
		$resultSaved = $this->object->insert( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLabel( 'new new name' );
		$itemExp->setConfig( ['client' => ['html' => ['catalog' => ['pagesize' => 24]]]] );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $item->getId() );

		$context = \TestHelper::context();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );
		$this->assertEquals( $item->getChildren(), $itemSaved->getChildren() );
		$this->assertEquals( $item->getRefId(), $itemSaved->getRefId() );
		$this->assertEquals( $item->getTheme(), $itemSaved->getTheme() );
		$this->assertEquals( $item->getLogos(), $itemSaved->getLogos() );
		$this->assertEquals( $item->getLogo(), $itemSaved->getLogo() );
		$this->assertEquals( $item->getIcon(), $itemSaved->getIcon() );

		$this->assertEquals( $context->editor(), $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getConfig(), $itemUpd->getConfig() );
		$this->assertEquals( $itemExp->getChildren(), $itemUpd->getChildren() );
		$this->assertEquals( $itemExp->getRefId(), $itemUpd->getRefId() );
		$this->assertEquals( $itemExp->getTheme(), $itemUpd->getTheme() );
		$this->assertEquals( $itemExp->getLogos(), $itemUpd->getLogos() );
		$this->assertEquals( $itemExp->getLogo(), $itemUpd->getLogo() );
		$this->assertEquals( $itemExp->getIcon(), $itemUpd->getIcon() );

		$this->assertEquals( $context->editor(), $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $item->getId() );
	}


	public function testFind()
	{
		$item = $this->object->find( 'unittest' );

		$this->assertEquals( 'unittest', $item->getCode() );
	}


	public function testGet()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );

		$a = $this->object->search( $search )->toArray();
		if( ( $expected = reset( $a ) ) === false ) {
			throw new \RuntimeException( 'Site item not found' );
		}

		$actual = $this->object->get( $expected->getId() );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $actual );
		$this->assertEquals( $expected, $actual );
	}


	public function testRate()
	{
		$item = $this->object->find( 'unittest' );
		$result = $this->object->rate( $item->getId(), '4.00', 5 );
		$item2 = $this->object->find( 'unittest' );

		$this->object->rate( $item->getId(), $item->getRating(), $item->getRatings() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $result );
		$this->assertEquals( '4.00', $item2->getRating() );
		$this->assertEquals( 5, $item2->getRatings() );
	}


	public function testSearch()
	{
		$siteid = $this->context->locale()->getSiteId();

		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'locale.site.id', null );
		$expr[] = $search->compare( '==', 'locale.site.siteid', $siteid );
		$expr[] = $search->compare( '==', 'locale.site.code', 'unittest' );
		$expr[] = $search->compare( '==', 'locale.site.label', 'Unit test site' );
		$expr[] = $search->compare( '=~', 'locale.site.config', '{' );
		$expr[] = $search->compare( '==', 'locale.site.icon', 'path/to/site-icon.png' );
		$expr[] = $search->compare( '=~', 'locale.site.logo', '{' );
		$expr[] = $search->compare( '==', 'locale.site.rating', '0.00' );
		$expr[] = $search->compare( '==', 'locale.site.ratings', 0 );
		$expr[] = $search->compare( '==', 'locale.site.refid', '1234' );
		$expr[] = $search->compare( '==', 'locale.site.theme', 'shop' );
		$expr[] = $search->compare( '==', 'locale.site.status', 1 );
		$expr[] = $search->compare( '>=', 'locale.site.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.site.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.site.editor', '' );

		$total = 0;
		$search->setConditions( $search->and( $expr ) )->order( 'sort:locale.site:position' );
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		//search with base criteria and total
		$search = $this->object->filter( true );
		$search->setConditions( $search->compare( '==', 'locale.site.code', array( 'unittest' ) ) );
		$search->slice( 0, 1 );

		$results = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $results ) );
		$this->assertGreaterThanOrEqual( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testGetSubManager()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetPath()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );

		$results = $this->object->search( $search )->toArray();

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$list = $this->object->getPath( $expected->getId() )->toArray();
		$this->assertArrayHasKey( $expected->getId(), $list );
		$this->assertEquals( $expected, $list[$expected->getId()] );
	}


	public function testGetTree()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );

		$expected = $this->object->search( $search )->first();

		$item = $this->object->getTree( $expected->getId() );
		$this->assertEquals( $expected, $item );
	}


	public function testGetTreeNoId()
	{
		$object = $this->getMockBuilder( \Aimeos\MShop\Locale\Manager\Site\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->onlyMethods( array( 'search' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'search' )
			->willReturn( map( [$object->create()] ) );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $object->getTree() );
	}


	public function testGetTreeNoItem()
	{
		$object = $this->getMockBuilder( \Aimeos\MShop\Locale\Manager\Site\Standard::class )
			->setConstructorArgs( array( $this->context ) )
			->onlyMethods( array( 'search' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'search' )
			->willReturn( map() );

		$this->expectException( \Aimeos\MShop\Locale\Exception::class );
		$object->getTree();
	}


	public function testGetTreeCache()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );

		$results = $this->object->search( $search )->toArray();

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$item = $this->object->getTree( $expected->getId() );
		$item2 = $this->object->getTree( $expected->getId() );

		$this->assertSame( $item, $item2 );
	}


	public function testGetTreeDefault()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'default' ) );
		$results = $this->object->search( $search )->toArray();

		$this->assertIsArray( $results );

		if( ( $expected = reset( $results ) ) !== false )
		{
			$item = $this->object->getTree();
			$this->assertEquals( $expected, $item );
		}
	}


	public function testMoveItem()
	{
		$this->expectException( \Aimeos\MShop\Locale\Exception::class );
		$this->object->move( '', '', '' );
	}
}
