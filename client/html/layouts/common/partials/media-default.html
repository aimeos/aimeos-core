<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

if( !isset( $this->item ) ) {
	return;
}

$enc = $this->encoder();
$boxAttributes = $this->get( 'boxAttributes', array() );
$itemAttributes = $this->get( 'itemAttributes', array() );

$item = $this->item;
$url = $item->getUrl();
$previewUrl = $item->getPreview();
$parts = explode( '/', $item->getMimetype() );

$boxattr = '';
foreach( $boxAttributes as $name => $value ) {
	$boxattr .= $name . ( $value != null ? '="' . $enc->attr( $value ) . '"' : '' ) . ' ';
}

$itemattr = '';
foreach( $itemAttributes as $name => $value ) {
	$itemattr .= $name . ( $value != null ? '="' . $enc->attr( $value ) . '"' : '' ) . ' ';
}

?>
<?php switch( $parts[0] ) : ?>
<?php	case 'audio': ?>
<audio <?php echo $boxattr; ?> >
	<source src="<?php echo $enc->attr( $this->content( $url ) ); ?>" title="<?php echo $enc->attr( $item->getName() ); ?>" type="<?php echo $enc->attr( $item->getMimetype() ); ?>" <?php echo $itemattr; ?> />
<?php		foreach( $item->getRefItems( 'media' ) as $item ) : ?>
	<source src="<?php echo $enc->attr( $this->content( $url ) ); ?>" title="<?php echo $enc->attr( $item->getName() ); ?>" type="<?php echo $enc->attr( $item->getMimetype() ); ?>" <?php echo $itemattr; ?> />
<?php		endforeach; ?>
<?php		echo $enc->html( $item->getName() ); ?>
</audio>
<?php	break; ?>
<?php	case 'video': ?>
<video <?php echo $boxattr; ?> >
	<source src="<?php echo $enc->attr( $this->content( $url ) ); ?>" title="<?php echo $enc->attr( $item->getName() ); ?>" type="<?php echo $enc->attr( $item->getMimetype() ); ?>" <?php echo $itemattr; ?> />
<?php		foreach( $item->getRefItems( 'media' ) as $item ) : ?>
	<source src="<?php echo $enc->attr( $this->content( $url ) ); ?>" title="<?php echo $enc->attr( $item->getName() ); ?>" type="<?php echo $enc->attr( $item->getMimetype() ); ?>" <?php echo $itemattr; ?> />
<?php		endforeach; ?>
<?php		echo $enc->html( $item->getName() ); ?>
</video>
<?php	break; ?>
<?php	case 'image': ?>
<div <?php echo $boxattr; ?> ><!--
	--><img src="<?php echo $enc->attr( $this->content( $previewUrl ) ); ?>" title="<?php echo $enc->attr( $item->getName() ); ?>" <?php echo $itemattr; ?> /><!--
<?php		foreach( $item->getRefItems( 'media' ) as $item ) : ?>
	--><img src="<?php echo $enc->attr( $this->content( $previewUrl ) ); ?>" title="<?php echo $enc->attr( $item->getName() ); ?>" <?php echo $itemattr; ?> /><!--
<?php		endforeach; ?>
--></div>
<?php	break; ?>
<?php	default: ?>
<a href="<?php echo $enc->attr( $this->content( $url ) ); ?>" <?php echo $boxattr ?> ><!--
	--><img src="<?php echo $enc->attr( $this->content( $previewUrl ) ); ?>" title="<?php echo $enc->attr( $item->getName() ); ?>" <?php echo $itemattr ?> /><!--
<?php		echo $enc->html( $item->getName() ); ?>
--></a>
<?php endswitch; ?>
