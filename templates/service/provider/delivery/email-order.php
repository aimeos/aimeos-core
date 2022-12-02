<?php

$csv = function( string $type, string $id, array $data ) {

	foreach( $data as $pos => $entry ) { // ltrim to invalidate Excel macros
		$data[$pos] = '"' . str_replace( '"', '""', ltrim( json_encode( $entry ), '@=+-' ) ) . '"';
	}

	return '"' . $type . '";"' . $id . '";' . join( ';', $data ) . PHP_EOL;
};


foreach( $this->get( 'orderItems', [] ) as $orderItem )
{
	$data = ['order.ordernumber' => $orderItem->getOrderNumber()] + $orderItem->toArray();

	echo $csv( 'invoice', $orderItem->getId(), $data );

	foreach( $orderItem->getAddresses()->krsort() as $type => $addresses )
	{
		foreach( $addresses as $address ) {
			echo $csv( 'address', $orderItem->getId(), $address->toArray() );
		}
	}

	foreach( $orderItem->getProducts() as $product )
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

	foreach( $orderItem->getServices()->krsort() as $type => $services )
	{
		foreach( $services as $service )
		{
			$list = $service->toArray();

			foreach( $service->getAttributeItems() as $attrItem )
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

	echo PHP_EOL;
}

?>