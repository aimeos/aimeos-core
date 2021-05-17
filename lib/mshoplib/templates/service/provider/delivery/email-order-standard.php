<?php

$csv = function( string $type, string $id, array $data ) {

	foreach( $data as $pos => $entry ) {
		$data[$pos] = '"' . str_replace( '"', '""', $entry ) . '"' . PHP_EOL;
	}

	return '"' . $type . '";"' . $id . '";' . join( ';', $data );
};


$baseItems = $this->get( 'baseItems', [] );

foreach( $this->get( 'orderItems', [] ) as $orderItem )
{
	if( ( $baseItem = $baseItems[$orderItem->getBaseId()] ?? null ) === null ) {
		continue;
	}

	$addresses = $baseItem->getAddresses();
	$products = $baseItem->getProducts();
	$services = $baseItem->getServices();

	$data = array_merge( $orderItem->toArray(), $baseItem->toArray() );

	echo $csv( 'invoice', $orderItem->getId(), $data );

	foreach( $addresses as $type => $addresses )
	{
		foreach( $addresses as $address ) {
			echo $csv( 'address', $orderItem->getId(), $address->toArray() );
		}
	}

	foreach( $products->getProducts() as $product )
	{
		$list = $product->toArray();

		foreach( $product->getAttributeItems() as $attrItem )
		{
			foreach( $attrItem->toArray( true ) as $key => $value )
			{
				if( isset( $list[$key] ) ) {
					$list[$key] .= "\n" . $value;
				} else {
					$list[$key] = $value;
				}
			}
		}

		echo $csv( 'product', $orderItem->getId(), $list );
	}

	foreach( $baseItem->getAddresses()->krsort() as $type => $addresses )
	{
		foreach( $addresses as $address ) {
			echo $csv( 'address', $orderItem->getId(), $address->toArray() );
		}
	}

	foreach( $baseItem->getServices()->krsort() as $type => $services )
	{
		foreach( $services as $service )
		{
			$list = $product->toArray();

			foreach( $product->getAttributeItems() as $attrItem )
			{
				foreach( $attrItem->toArray( true ) as $key => $value )
				{
					if( isset( $list[$key] ) ) {
						$list[$key] .= "\n" . $value;
					} else {
						$list[$key] = $value;
					}
				}
			}

			echo $csv( 'service', $orderItem->getId(), $list );
		}
	}

	echo PHP_EOL . PHP_EOL;
}

?>