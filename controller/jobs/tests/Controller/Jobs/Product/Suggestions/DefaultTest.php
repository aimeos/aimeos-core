<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_Jobs_Product_Suggestions_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_context;
	private $_arcavias;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$this->_arcavias = TestHelper::getArcavias();

		$this->_object = new Controller_Jobs_Product_Suggestions_Default( $this->_context, $this->_arcavias );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testGetName()
	{
		$this->assertEquals( 'Product suggestions', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Generation of product suggestions';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$stub = $this->getMockBuilder( 'Controller_Jobs_Product_Suggestions_Default' )
		->setConstructorArgs( array( $this->_context, $this->_arcavias ) )
		->setMethods( array( '_createProductListSuggestions', '_removeProductSuggestions' ) )
		->getMock();


		$stub->expects($this->exactly(10))
		->method('_createProductListSuggestions')
		->with( $this->greaterThan(0),
				$this->anything(),
				$this->equalTo($this->_getProductListTypeId()),
				$this->isInstanceOf('MShop_Common_Manager_Abstract'));

		$stub->expects($this->exactly(10))
		->method('_removeProductSuggestions');

		$stub->run();
	}


	/**
	 * Gets id of the product list type item with code suggestion.
	 *
	 * @param MShop_Product_Manager_List_Type_Default $prodListMgr Manager for list types of product domain
	 * @param string $code Code of product list type
	 */
	protected function _getProductListTypeId()
	{
		$prodListTypeMgr = MShop_Factory::createManager($this->_context, 'product/list/type');
		$code = 'suggestion';

		$search = $prodListTypeMgr->createSearch();
		$search->setConditions( $search->compare( '==', 'product.list.type.code', $code ) );
		$items = $prodListTypeMgr->searchItems( $search );

		if( ( $listType = reset( $items ) ) === false ) {
			throw new Exception('No product list type with code ' . $code . ' found' );
		}

		return $listType->getId();
	}
}