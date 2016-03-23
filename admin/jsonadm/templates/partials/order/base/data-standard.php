<?php

$options = 0;
if( defined( 'JSON_PRETTY_PRINT' ) ) {
	$options = JSON_PRETTY_PRINT;
}

$build = function( \Aimeos\MShop\Common\Item\Iface $item, array $fields, array $childItems )
{
	$id = $item->getId();
	$attributes = $item->toArray();
	$type = $item->getResourceType();

	if( isset( $fields[$type] ) ) {
		$attributes = array_intersect_key( $attributes, $fields[$type] );
	}

	$result = array(
		'id' => $id,
		'type' => $type,
		'attributes' => $attributes,
		'relationships' => array()
	);

	foreach( $childItems as $childItem )
	{
		if( $childItem->getBaseId() == $id )
		{
			$type = $childItem->getResourceType();
			$result['relationships'][$type][] = array( 'data' => array( 'id' => $childItem->getId(), 'type' => $type ) );
		}
	}

	return $result;
};


$fields = $this->param( 'fields', array() );

foreach( (array) $fields as $resource => $list ) {
	$fields[$resource] = array_flip( explode( ',', $list ) );
}


$data = $this->get( 'data', array() );
$childItems = $this->get( 'childItems', array() );

if( is_array( $data ) )
{
	$response = array();

	foreach( $data as $item ) {
		$response[] = $build( $item, $fields, $childItems );
	}
}
elseif( $data !== null )
{
	$response = $build( $data, $fields, $childItems );
}
else
{
	$response = null;
}


echo json_encode( $response, $options );