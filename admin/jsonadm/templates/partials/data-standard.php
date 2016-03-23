<?php

$options = 0;
if( defined( 'JSON_PRETTY_PRINT' ) ) {
	$options = JSON_PRETTY_PRINT;
}


$build = function( \Aimeos\MShop\Common\Item\Iface $item, array $fields, array $childItems, array $listItems )
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
		if( $childItem->getParentId() == $id )
		{
			$type = $childItem->getResourceType();
			$result['relationships'][$type][] = array( 'data' => array( 'id' => $childItem->getId(), 'type' => $type ) );
		}
	}

	foreach( $listItems as $listItem )
	{
		if( $listItem->getParentId() == $id )
		{
			$type = $listItem->getDomain();
			$result['relationships'][$type][] = array( 'data' => array( 'id' => $listItem->getRefId(), 'type' => $type, 'attributes' => $listItem->toArray() ) );
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
$listItems = $this->get( 'listItems', array() );

if( is_array( $data ) )
{
	$response = array();

	foreach( $data as $item ) {
		$response[] = $build( $item, $fields, $childItems, $listItems );
	}
}
elseif( $data !== null )
{
	$response = $build( $data, $fields, $childItems, $listItems );
}
else
{
	$response = null;
}


echo json_encode( $response, $options );