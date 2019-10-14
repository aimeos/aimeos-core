<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @author Taylor Otwell, Aimeos.org developers
 * @package MW
 */


namespace Aimeos\MW;


class MapTest extends \PHPUnit\Framework\TestCase
{
	public function testClear()
	{
		$c = new Map( ['foo', 'bar'] );
		$this->assertCount( 0, $c->clear() );
	}

	public function testCopy()
	{
		$c = new Map( ['foo', 'bar'] );
		$cp = $c->copy();
		$c->clear();
		$this->assertCount( 2, $cp );
	}

	public function testFirstReturnsFirstItemInMap()
	{
		$c = new Map( ['foo', 'bar'] );
		$this->assertEquals( 'foo', $c->first() );
	}

	public function testFirstWithCallback()
	{
		$data = new Map( ['foo', 'bar', 'baz'] );
		$result = $data->first( function( $value ) {
			return $value === 'bar';
		} );
		$this->assertEquals( 'bar', $result );
	}

	public function testFirstWithCallbackAndDefault()
	{
		$data = new Map( ['foo', 'bar'] );
		$result = $data->first( function( $value ) {
			return $value === 'baz';
		}, 'default' );
		$this->assertEquals( 'default', $result );
	}

	public function testFirstWithDefaultAndWithoutCallback()
	{
		$data = new Map;
		$result = $data->first( null, 'default' );
		$this->assertEquals( 'default', $result );
	}

	public function testLastReturnsLastItemInMap()
	{
		$c = new Map( ['foo', 'bar'] );
		$this->assertEquals( 'bar', $c->last() );
	}

	public function testLastWithCallback()
	{
		$data = new Map( [100, 200, 300] );
		$result = $data->last( function( $value ) {
			return $value < 250;
		} );
		$this->assertEquals( 200, $result );
		$result = $data->last( function( $value, $key ) {
			return $key < 2;
		} );
		$this->assertEquals( 200, $result );
	}

	public function testLastWithCallbackAndDefault()
	{
		$data = new Map( ['foo', 'bar'] );
		$result = $data->last( function( $value ) {
			return $value === 'baz';
		}, 'default' );
		$this->assertEquals( 'default', $result );
	}

	public function testLastWithDefaultAndWithoutCallback()
	{
		$data = new Map;
		$result = $data->last( null, 'default' );
		$this->assertEquals( 'default', $result );
	}

	public function testPopReturnsAndRemovesLastItemInMap()
	{
		$c = new Map( ['foo', 'bar'] );

		$this->assertEquals( 'bar', $c->pop() );
		$this->assertEquals( 'foo', $c->first() );
	}

	public function testShiftReturnsAndRemovesFirstItemInMap()
	{
		$c = new Map( ['foo', 'bar'] );

		$this->assertEquals( 'foo', $c->shift() );
		$this->assertEquals( 'bar', $c->first() );
	}

	public function testEmptyMapIsEmpty()
	{
		$c = new Map;

		$this->assertTrue( $c->isEmpty() );
	}

	public function testMapIsConstructed()
	{
		$map = new Map;
		$this->assertEmpty( $map->toArray() );
	}

	public function testMapShuffle()
	{
		$map = new Map( range( 0, 100, 10 ) );

		$firstRandom = $map->copy()->shuffle();
		$secondRandom = $map->copy()->shuffle();

		$this->assertNotEquals( $firstRandom, $secondRandom );
	}

	public function testGetArray()
	{
		$map = new Map;

		$class = new \ReflectionClass( $map );
		$method = $class->getMethod( 'getArray' );
		$method->setAccessible( true );

		$items = new \ArrayIterator( ['foo' => 'bar'] );
		$array = $method->invokeArgs( $map, [$items] );
		$this->assertSame( ['foo' => 'bar'], $array );

		$items = new Map( ['foo' => 'bar'] );
		$array = $method->invokeArgs( $map, [$items] );
		$this->assertSame( ['foo' => 'bar'], $array );

		$items = ['foo' => 'bar'];
		$array = $method->invokeArgs( $map, [$items] );
		$this->assertSame( ['foo' => 'bar'], $array );
	}

	public function testOffsetAccess()
	{
		$c = new Map( ['name' => 'test'] );
		$this->assertEquals( 'test', $c['name'] );
		$c['name'] = 'me';
		$this->assertEquals( 'me', $c['name'] );
		$this->assertTrue( isset( $c['name'] ) );
		unset( $c['name'] );
		$this->assertFalse( isset( $c['name'] ) );
		$c[] = 'jason';
		$this->assertEquals( 'jason', $c[0] );
	}

	public function testArrayAccessOffsetExists()
	{
		$c = new Map( ['foo', 'bar'] );
		$this->assertTrue( $c->offsetExists( 0 ) );
		$this->assertTrue( $c->offsetExists( 1 ) );
		$this->assertFalse( $c->offsetExists( 1000 ) );
	}

	public function testArrayAccessOffsetGet()
	{
		$c = new Map( ['foo', 'bar'] );
		$this->assertEquals( 'foo', $c->offsetGet( 0 ) );
		$this->assertEquals( 'bar', $c->offsetGet( 1 ) );
	}

	public function testArrayAccessOffsetSet()
	{
		$c = new Map( ['foo', 'foo'] );

		$c->offsetSet( 1, 'bar' );
		$this->assertEquals( 'bar', $c[1] );

		$c->offsetSet( null, 'qux' );
		$this->assertEquals( 'qux', $c[2] );
	}

	public function testArrayAccessOffsetUnset()
	{
		$c = new Map( ['foo', 'bar'] );

		$c->offsetUnset( 1 );
		$this->assertFalse( isset( $c[1] ) );
	}

	public function testRemoveSingleKey()
	{
		$c = new Map( ['foo', 'bar'] );
		$c->remove( 0 );
		$this->assertFalse( isset( $c['foo'] ) );

		$c = new Map( ['foo' => 'bar', 'baz' => 'qux'] );
		$c->remove( 'foo' );
		$this->assertFalse( isset( $c['foo'] ) );
	}

	public function testRemoveArrayOfKeys()
	{
		$c = new Map( ['foo', 'bar', 'baz'] );
		$c->remove( [0, 2] );
		$this->assertFalse( isset( $c[0] ) );
		$this->assertFalse( isset( $c[2] ) );
		$this->assertTrue( isset( $c[1] ) );

		$c = new Map( ['name' => 'test', 'foo' => 'bar', 'baz' => 'qux'] );
		$c->remove( ['foo', 'baz'] );
		$this->assertFalse( isset( $c['foo'] ) );
		$this->assertFalse( isset( $c['baz'] ) );
		$this->assertTrue( isset( $c['name'] ) );
	}

	public function testCountable()
	{
		$c = new Map( ['foo', 'bar'] );
		$this->assertCount( 2, $c );
	}

	public function testIterable()
	{
		$c = new Map( ['foo'] );
		$this->assertInstanceOf( \ArrayIterator::class, $c->getIterator() );
		$this->assertEquals( ['foo'], $c->getIterator()->getArrayCopy() );
	}

	public function testFilter()
	{
		$c = new Map( [['id' => 1, 'name' => 'Hello'], ['id' => 2, 'name' => 'World']] );
		$this->assertEquals( [1 => ['id' => 2, 'name' => 'World']], $c->filter( function( $item ) {
			return $item['id'] == 2;
		} )->toArray() );

		$c = new Map( ['', 'Hello', '', 'World'] );
		$this->assertEquals( ['Hello', 'World'], $c->filter()->values()->toArray() );

		$c = new Map( ['id' => 1, 'first' => 'Hello', 'second' => 'World'] );
		$this->assertEquals( ['first' => 'Hello', 'second' => 'World'], $c->filter( function( $item, $key ) {
			return $key != 'id';
		} )->toArray() );
	}

	public function testValues()
	{
		$c = new Map( [['id' => 1, 'name' => 'Hello'], ['id' => 2, 'name' => 'World']] );
		$this->assertEquals( [['id' => 2, 'name' => 'World']], $c->filter( function( $item ) {
			return $item['id'] == 2;
		} )->values()->toArray() );
	}

	public function testMergeArray()
	{
		$c = new Map( ['name' => 'Hello'] );
		$this->assertEquals( ['name' => 'Hello', 'id' => 1], $c->merge( ['id' => 1] )->toArray() );
	}

	public function testMergeMap()
	{
		$c = new Map( ['name' => 'Hello'] );
		$this->assertEquals( ['name' => 'World', 'id' => 1], $c->merge( new Map( ['name' => 'World', 'id' => 1] ) )->toArray() );
	}

	public function testReplaceArray()
	{
		$c = new Map( ['a', 'b', 'c'] );
		$this->assertEquals( ['a', 'd', 'e'], $c->replace( [1 => 'd', 2 => 'e'] )->toArray() );
	}

	public function testReplaceMap()
	{
		$c = new Map( ['a', 'b', 'c'] );
		$this->assertEquals(
			['a', 'd', 'e'],
			$c->replace( new Map( [1 => 'd', 2 => 'e'] ) )->toArray()
		);
	}

	public function testReplaceRecursiveArray()
	{
		$c = new Map( ['a', 'b', ['c', 'd']] );
		$this->assertEquals( ['z', 'b', ['c', 'e']], $c->replace( ['z', 2 => [1 => 'e']] )->toArray() );
	}

	public function testReplaceRecursiveMap()
	{
		$c = new Map( ['a', 'b', ['c', 'd']] );
		$this->assertEquals(
			['z', 'b', ['c', 'e']],
			$c->replace( new Map( ['z', 2 => [1 => 'e']] ) )->toArray()
		);
	}

	public function testUnionArray()
	{
		$c = new Map( ['name' => 'Hello'] );
		$this->assertEquals( ['name' => 'Hello', 'id' => 1], $c->union( ['id' => 1] )->toArray() );
	}

	public function testUnionMap()
	{
		$c = new Map( ['name' => 'Hello'] );
		$this->assertEquals( ['name' => 'Hello', 'id' => 1], $c->union( new Map( ['name' => 'World', 'id' => 1] ) )->toArray() );
	}

	public function testDiff()
	{
		$c = new Map( ['id' => 1, 'first_word' => 'Hello'] );
		$this->assertEquals( ['id' => 1], $c->diff( new Map( ['first_word' => 'Hello', 'last_word' => 'World'] ) )->toArray() );
	}

	public function testDiffUsingWithMap()
	{
		$c = new Map( ['en_GB', 'fr', 'HR'] );
		// demonstrate that diffKeys wont support case insensitivity
		$this->assertEquals( ['en_GB', 'fr', 'HR'], $c->diff( new Map( ['en_gb', 'hr'] ) )->values()->toArray() );
	}

	public function testDiffKeys()
	{
		$c1 = new Map( ['id' => 1, 'first_word' => 'Hello'] );
		$c2 = new Map( ['id' => 123, 'foo_bar' => 'Hello'] );
		$this->assertEquals( ['first_word' => 'Hello'], $c1->diffKeys( $c2 )->toArray() );
	}

	public function testDiffKeysFunction()
	{
		$c1 = new Map( ['id' => 1, 'first_word' => 'Hello'] );
		$c2 = new Map( ['ID' => 123, 'foo_bar' => 'Hello'] );
		// demonstrate that diffKeys wont support case insensitivity
		$this->assertEquals( ['id'=>1, 'first_word'=> 'Hello'], $c1->diffKeys( $c2 )->toArray() );
		// allow for case insensitive difference
		$this->assertEquals( ['first_word' => 'Hello'], $c1->diffKeys( $c2, 'strcasecmp' )->toArray() );
	}

	public function testDiffAssoc()
	{
		$c1 = new Map( ['id' => 1, 'first_word' => 'Hello', 'not_affected' => 'value'] );
		$c2 = new Map( ['id' => 123, 'foo_bar' => 'Hello', 'not_affected' => 'value'] );
		$this->assertEquals( ['id' => 1, 'first_word' => 'Hello'], $c1->diffAssoc( $c2 )->toArray() );
	}

	public function testDiffAssocFunction()
	{
		$c1 = new Map( ['a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red'] );
		$c2 = new Map( ['A' => 'green', 'yellow', 'red'] );
		// demonstrate that the case of the keys will affect the output when diffAssoc is used
		$this->assertEquals( ['a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red'], $c1->diffAssoc( $c2 )->toArray() );
		// allow for case insensitive difference
		$this->assertEquals( ['b' => 'brown', 'c' => 'blue', 'red'], $c1->diffAssoc( $c2, 'strcasecmp' )->toArray() );
	}

	public function testEach()
	{
		$c = new Map( $original = [1, 2, 'foo' => 'bar', 'bam' => 'baz'] );

		$result = [];
		$c->each( function( $item, $key ) use ( &$result ) {
			$result[$key] = $item;
		} );
		$this->assertEquals( $original, $result );

		$result = [];
		$c->each( function( $item, $key ) use ( &$result ) {
			$result[$key] = $item;
			if( is_string( $key ) ) {
				return false;
			}
		} );
		$this->assertEquals( [1, 2, 'foo' => 'bar'], $result );
	}

	public function testIntersec()
	{
		$c = new Map( ['id' => 1, 'first_word' => 'Hello'] );
		$i = new Map( ['first_world' => 'Hello', 'last_word' => 'World'] );
		$this->assertEquals( ['first_word' => 'Hello'], $c->intersect( $i )->toArray() );
	}

	public function testIntersecFunction()
	{
		$c = new Map( ['id' => 1, 'first_word' => 'Hello', 'last_word' => 'World'] );
		$i = new Map( ['first_world' => 'Hello', 'last_world' => 'world'] );
		$this->assertEquals( ['first_word' => 'Hello', 'last_word' => 'World'], $c->intersect( $i, 'strcasecmp' )->toArray() );
	}

	public function testIntersectAssoc()
	{
		$c = new Map( ['id' => 1, 'name' => 'Mateus', 'age' => 18] );
		$i = new Map( ['name' => 'Mateus', 'firstname' => 'Mateus'] );
		$this->assertEquals( ['name' => 'Mateus'], $c->intersectAssoc( $i )->toArray() );
	}

	public function testIntersecAssocFunction()
	{
		$c = new Map( ['id' => 1, 'first_word' => 'Hello', 'last_word' => 'World'] );
		$i = new Map( ['first_word' => 'hello', 'Last_word' => 'world'] );
		$this->assertEquals( ['first_word' => 'Hello'], $c->intersectAssoc( $i, 'strcasecmp' )->toArray() );
	}

	public function testIntersectKeys()
	{
		$c = new Map( ['id' => 1, 'name' => 'Mateus', 'age' => 18] );
		$i = new Map( ['name' => 'Mateus', 'surname' => 'Guimaraes'] );
		$this->assertEquals( ['name' => 'Mateus'], $c->intersectKeys( $i )->toArray() );
	}

	public function testIntersecKeysFunction()
	{
		$c = new Map( ['id' => 1, 'first_word' => 'Hello', 'last_word' => 'World'] );
		$i = new Map( ['First_word' => 'Hello', 'last_word' => 'world'] );
		$this->assertEquals( ['first_word' => 'Hello', 'last_word' => 'World'], $c->intersectKeys( $i, 'strcasecmp' )->toArray() );
	}

	public function testUnique()
	{
		$c = new Map( ['Hello', 'World', 'World'] );
		$this->assertEquals( ['Hello', 'World'], $c->unique()->toArray() );
	}

	public function testSort()
	{
		$data = ( new Map( [5, 3, 1, 2, 4] ) )->sort();
		$this->assertEquals( [1, 2, 3, 4, 5], $data->values()->toArray() );

		$data = ( new Map( [-1, -3, -2, -4, -5, 0, 5, 3, 1, 2, 4] ) )->sort();
		$this->assertEquals( [-5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5], $data->values()->toArray() );

		$data = ( new Map( ['foo', 'bar-10', 'bar-1'] ) )->sort();
		$this->assertEquals( ['bar-1', 'bar-10', 'foo'], $data->values()->toArray() );
	}

	public function testKsort()
	{
		$data = new Map( ['b' => 'me', 'a' => 'test'] );

		$this->assertSame( ['a' => 'test', 'b' => 'me'], $data->ksort()->toArray() );
	}

	public function testReverse()
	{
		$data = new Map( ['hello', 'world'] );
		$reversed = $data->reverse();

		$this->assertSame( [1 => 'world', 0 => 'hello'], $reversed->toArray() );

		$data = new Map( ['name' => 'test', 'last' => 'user'] );
		$reversed = $data->reverse();

		$this->assertSame( ['last' => 'user', 'name' => 'test'], $reversed->toArray() );
	}

	public function testHas()
	{
		$data = new Map( ['id' => 1, 'first' => 'Hello', 'second' => 'World'] );
		$this->assertTrue( $data->has( 'first' ) );
		$this->assertFalse( $data->has( 'third' ) );
	}

	public function testMethod()
	{
		Map::method( 'foo', function() {
			return $this->filter( function( $item ) {
				return strpos( $item, 'a' ) === 0;
			})
				->unique()
				->values();
		} );

		$c = new Map( ['a', 'a', 'aa', 'aaa', 'bar'] );

		$this->assertSame( ['a', 'aa', 'aaa'], $c->foo()->toArray() );
	}

	public function testMakeMethodFromMap()
	{
		$firstMap = Map::from( ['foo' => 'bar'] );
		$secondMap = Map::from( $firstMap );
		$this->assertEquals( ['foo' => 'bar'], $secondMap->toArray() );
	}

	public function testMakeMethodFromArray()
	{
		$map = Map::from( ['foo' => 'bar'] );
		$this->assertEquals( ['foo' => 'bar'], $map->toArray() );
	}

	public function testConstructMethodFromMap()
	{
		$firstMap = new Map( ['foo' => 'bar'] );
		$secondMap = new Map( $firstMap );
		$this->assertEquals( ['foo' => 'bar'], $secondMap->toArray() );
	}

	public function testConstructMethodFromArray()
	{
		$map = new Map( ['foo' => 'bar'] );
		$this->assertEquals( ['foo' => 'bar'], $map->toArray() );
	}

	public function testSplice()
	{
		$data = new Map( ['foo', 'baz'] );
		$data->splice( 1 );
		$this->assertEquals( ['foo'], $data->toArray() );

		$data = new Map( ['foo', 'baz'] );
		$data->splice( 1, 0, 'bar' );
		$this->assertEquals( ['foo', 'bar', 'baz'], $data->toArray() );

		$data = new Map( ['foo', 'baz'] );
		$data->splice( 1, 1 );
		$this->assertEquals( ['foo'], $data->toArray() );

		$data = new Map( ['foo', 'baz'] );
		$cut = $data->splice( 1, 1, 'bar' );
		$this->assertEquals( ['foo', 'bar'], $data->toArray() );
		$this->assertEquals( ['baz'], $cut->toArray() );
	}

	public function testMap()
	{
		$data = new Map( ['first' => 'test', 'last' => 'user'] );
		$data = $data->map( function( $item, $key ) {
			return $key . '-' . strrev( $item );
		} );
		$this->assertEquals( ['first' => 'first-tset', 'last' => 'last-resu'], $data->toArray() );
	}

	public function testPullRetrievesItemFromMap()
	{
		$c = new Map( ['foo', 'bar'] );

		$this->assertEquals( 'foo', $c->pull( 0 ) );
	}

	public function testPullRemovesItemFromMap()
	{
		$c = new Map( ['foo', 'bar'] );
		$c->pull( 0 );
		$this->assertEquals( [1 => 'bar'], $c->toArray() );
	}

	public function testPullReturnsDefault()
	{
		$c = new Map( [] );
		$value = $c->pull( 0, 'foo' );
		$this->assertEquals( 'foo', $value );
	}

	public function testSearch()
	{
		$c = new Map( [false, 0, 1, [], ''] );
		$this->assertNull( $c->search( 'false' ) );
		$this->assertNull( $c->search( '1' ) );
		$this->assertEquals( 0, $c->search( false ) );
		$this->assertEquals( 1, $c->search( 0 ) );
		$this->assertEquals( 2, $c->search( 1 ) );
		$this->assertEquals( 3, $c->search( [] ) );
		$this->assertEquals( 4, $c->search( '' ) );
	}

	public function testSearchReturnsNullWhenItemIsNotFound()
	{
		$c = new Map( [1, 2, 3, 4, 5, 'foo' => 'bar'] );

		$this->assertNull( $c->search( 6 ) );
		$this->assertNull( $c->search( 'foo' ) );
		$this->assertNull( $c->search( function( $value ) {
			return $value < 1 && is_numeric( $value );
		} ) );
		$this->assertNull( $c->search( function( $value ) {
			return $value == 'nope';
		} ) );
	}

	public function testKeys()
	{
		$c = new Map( ['name' => 'test', 'last' => 'user'] );
		$this->assertEquals( ['name', 'last'], $c->keys()->toArray() );
	}

	public function testUnshift()
	{
		$c = new Map( ['one', 'two', 'three', 'four'] );
		$this->assertEquals( ['zero', 'one', 'two', 'three', 'four'], $c->unshift( 'zero' )->toArray() );

		$c = new Map( ['one' => 1, 'two' => 2] );
		$this->assertEquals( ['zero' => 0, 'one' => 1, 'two' => 2], $c->unshift( 0, 'zero' )->toArray() );
	}

	public function testConcatWithArray()
	{
		$expected = [
			0 => 4,
			1 => 5,
			2 => 6,
			3 => 'a',
			4 => 'b',
			5 => 'c',
			6 => 'Jonny',
			7 => 'from',
			8 => 'Laroe',
			9 => 'Jonny',
			10 => 'from',
			11 => 'Laroe',
		];

		$map = new Map( [4, 5, 6] );
		$map = $map->concat( ['a', 'b', 'c'] );
		$map = $map->concat( ['who' => 'Jonny', 'preposition' => 'from', 'where' => 'Laroe'] );
		$actual = $map->concat( ['who' => 'Jonny', 'preposition' => 'from', 'where' => 'Laroe'] )->toArray();

		$this->assertSame( $expected, $actual );
	}

	public function testConcatWithMap()
	{
		$expected = [
			0 => 4,
			1 => 5,
			2 => 6,
			3 => 'a',
			4 => 'b',
			5 => 'c',
			6 => 'Jonny',
			7 => 'from',
			8 => 'Laroe',
			9 => 'Jonny',
			10 => 'from',
			11 => 'Laroe',
		];

		$firstMap = new Map( [4, 5, 6] );
		$secondMap = new Map( ['a', 'b', 'c'] );
		$thirdMap = new Map( ['who' => 'Jonny', 'preposition' => 'from', 'where' => 'Laroe'] );
		$firstMap = $firstMap->concat( $secondMap );
		$firstMap = $firstMap->concat( $thirdMap );
		$actual = $firstMap->concat( $thirdMap )->toArray();

		$this->assertSame( $expected, $actual );
	}

	public function testReduce()
	{
		$data = new Map( [1, 2, 3] );
		$this->assertEquals( 6, $data->reduce( function( $carry, $element ) {
			return $carry += $element;
		} ) );
	}

	public function testPipe()
	{
		$map = new Map( [1, 2, 3] );

		$this->assertEquals( 3, $map->pipe( function( $map ) {
			return $map->last();
		} ) );
	}

	public function testSliceOffset()
	{
		$map = new Map( [1, 2, 3, 4, 5, 6, 7, 8] );
		$this->assertEquals( [4, 5, 6, 7, 8], $map->slice( 3 )->values()->toArray() );
	}

	public function testSliceNegativeOffset()
	{
		$map = new Map( [1, 2, 3, 4, 5, 6, 7, 8] );
		$this->assertEquals( [6, 7, 8], $map->slice(-3)->values()->toArray() );
	}

	public function testSliceOffsetAndLength()
	{
		$map = new Map( [1, 2, 3, 4, 5, 6, 7, 8] );
		$this->assertEquals( [4, 5, 6], $map->slice( 3, 3 )->values()->toArray() );
	}

	public function testSliceOffsetAndNegativeLength()
	{
		$map = new Map( [1, 2, 3, 4, 5, 6, 7, 8] );
		$this->assertEquals( [4, 5, 6, 7], $map->slice( 3, -1 )->values()->toArray() );
	}

	public function testSliceNegativeOffsetAndLength()
	{
		$map = new Map( [1, 2, 3, 4, 5, 6, 7, 8] );
		$this->assertEquals( [4, 5, 6], $map->slice(-5, 3)->values()->toArray() );
	}

	public function testSliceNegativeOffsetAndNegativeLength()
	{
		$map = new Map( [1, 2, 3, 4, 5, 6, 7, 8] );
		$this->assertEquals( [3, 4, 5, 6], $map->slice(-6, -2)->values()->toArray() );
	}

	public function testMapFromTraversable()
	{
		$map = new Map( new \ArrayObject( [1, 2, 3] ) );
		$this->assertEquals( [1, 2, 3], $map->toArray() );
	}

	public function testMapFromTraversableWithKeys()
	{
		$map = new Map( new \ArrayObject( ['foo' => 1, 'bar' => 2, 'baz' => 3] ) );
		$this->assertEquals( ['foo' => 1, 'bar' => 2, 'baz' => 3], $map->toArray() );
	}

	public function testHasReturnsValidResults()
	{
		$map = new Map( ['foo' => 'one', 'bar' => 'two', 1 => 'three'] );
		$this->assertTrue( $map->has( 'foo' ) );
	}

	public function testSetAddsItemToMap()
	{
		$map = new Map;
		$this->assertSame( [], $map->toArray() );
		$map->set( 'foo', 1 );
		$this->assertSame( ['foo' => 1], $map->toArray() );
		$map->set( 'bar', ['nested' => 'two'] );
		$this->assertSame( ['foo' => 1, 'bar' => ['nested' => 'two']], $map->toArray() );
		$map->set( 'foo', 3 );
		$this->assertSame( ['foo' => 3, 'bar' => ['nested' => 'two']], $map->toArray() );
	}

	public function testGetWithNullReturnsNull()
	{
		$map = new Map( [1, 2, 3] );
		$this->assertNull( $map->get( null ) );
	}
}
