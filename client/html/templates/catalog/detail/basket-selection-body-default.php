<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();
$prodDeps = json_encode( $this->get( 'selectionProductDependencies', new stdClass() ) );
$attrDeps = json_encode( $this->get( 'selectionAttributeDependencies', new stdClass() ) );

$params = array(
	'selectionProducts' => $this->get( 'selectionProducts', array() ),
	'selectionAttributeItems' => $this->get( 'selectionAttributeItems', array() ),
	'selectionAttributeTypeDependencies' => $this->get( 'selectionAttributeTypeDependencies', array() ),
);

?>
<div class="catalog-detail-basket-selection" data-proddeps="<?php echo $enc->attr( $prodDeps ); ?>" data-attrdeps="<?php echo $enc->attr( $attrDeps ); ?>">
<?php echo $this->partial( 'client/html/common/partials/selection', 'common/partials/selection-default.php', $params ); ?>
<?php echo $this->get( 'selectionBody' ); ?>
</div>
