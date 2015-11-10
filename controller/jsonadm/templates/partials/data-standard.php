<?php

$options = 0;
if( defined( 'JSON_PRETTY_PRINT' ) ) {
	$options = JSON_PRETTY_PRINT;
}


$build = function( \Aimeos\MShop\Common\Item\Iface $item, array $fields )
{
	$attributes = $item->toArray();
	$type = $item->getResourceType();

	if( isset( $fields[$type] ) ) {
		$attributes = array_intersect_key( $attributes, $fields[$type] );
	}

	$result = array(
		'id' => $item->getId(),
		'type' => $type,
		'attributes' => $attributes,
		'relationships' => array()
	);

	if( $item instanceof \Aimeos\MShop\Common\Item\ListRef\Iface )
	{
		foreach( $item->getRefItems() as $domain => $refItems )
		{
			foreach( $refItems as $id => $refItem ) {
				$result['relationships'][] = array( 'data' => array( 'id' => $id, 'type' => $refItem->getResourceType() ) );
			}
		}
	}

	return $result;
};


$fields = $this->param( 'fields', array() );

foreach( (array) $fields as $resource => $list ) {
	$fields[$resource] = array_flip( explode( ',', $list ) );
}


$data = $this->get( 'data', array() );

if( is_array( $data ) )
{
	$response = array();

	foreach( $data as $item ) {
		$response[] = $build( $item, $fields );
	}
}
elseif( $data !== null )
{
	$response = $build( $data, $fields );
}
else
{
	$response = null;
}


echo json_encode( $response, $options );