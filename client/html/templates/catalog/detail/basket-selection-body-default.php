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

/** client/html/common/partials/selection
 * Relative path to the variant attribute partial template file
 *
 * Partials are templates which are reused in other templates and generate
 * reoccuring blocks filled with data from the assigned values. The selection
 * partial creates an HTML block for a list of variant product attributes
 * assigned to a selection product a customer must select from.
 *
 * The partial template files are usually stored in the templates/partials/ folder
 * of the core or the extensions. The configured path to the partial file must
 * be relative to the templates/ folder, e.g. "partials/selection-default.php".
 *
 * @param string Relative path to the template file
 * @since 2015.04
 * @category Developer
 * @see client/html/common/partials/attribute
 */

?>
<div class="catalog-detail-basket-selection" data-proddeps="<?php echo $enc->attr( $prodDeps ); ?>" data-attrdeps="<?php echo $enc->attr( $attrDeps ); ?>">
<?php echo $this->partial( $this->config( 'client/html/common/partials/selection', 'common/partials/selection-default.php' ), $params ); ?>
<?php echo $this->get( 'selectionBody' ); ?>
</div>
