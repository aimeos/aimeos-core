<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\MShop\Policy;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	public function testIsGroups()
	{
		$this->assertTrue( \Aimeos\MShop\Policy\Base::isGroups( [] ) );
		$this->assertTrue( \Aimeos\MShop\Policy\Base::isGroups( ['editor'] ) );
		$this->assertFalse( \Aimeos\MShop\Policy\Base::isGroups( ['get' => ['editor']] ) );
		$this->assertTrue( \Aimeos\MShop\Policy\Base::isGroups( 'editor' ) );
		$this->assertFalse( \Aimeos\MShop\Policy\Base::isGroups( '' ) );
		$this->assertFalse( \Aimeos\MShop\Policy\Base::isGroups( null ) );
	}


	public function testIsResource()
	{
		$this->assertTrue( \Aimeos\MShop\Policy\Base::isResource( ['get' => ['editor']] ) );
		$this->assertTrue( \Aimeos\MShop\Policy\Base::isResource( ['get' => []] ) );
		$this->assertFalse( \Aimeos\MShop\Policy\Base::isResource( [] ) );
		$this->assertTrue( \Aimeos\MShop\Policy\Base::isResource( ['groups' => 'admin'] ) );
		$this->assertTrue( \Aimeos\MShop\Policy\Base::isResource( ['get' => 'editor', 'save' => ['admin']] ) );
		$this->assertFalse( \Aimeos\MShop\Policy\Base::isResource( ['get' => ''] ) );
		$this->assertFalse( \Aimeos\MShop\Policy\Base::isResource( ['get' => 1] ) );
		$this->assertFalse( \Aimeos\MShop\Policy\Base::isResource( ['editor'] ) );
		$this->assertFalse( \Aimeos\MShop\Policy\Base::isResource( ['admin', 'editor'] ) );
		$this->assertFalse( \Aimeos\MShop\Policy\Base::isResource( null ) );
	}
}
