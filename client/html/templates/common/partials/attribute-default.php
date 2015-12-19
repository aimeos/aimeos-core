<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

/** client/html/catalog/detail/basket/attribute/type
 * List of layout types for the optional attributes
 *
 * Each product can contain optional attributes and this configuration setting
 * allows you to change how these attributs will be displayed, either as
 * drop-down menu (value: "select") or as list of radio buttons (value:
 * "radio").
 *
 * The key for each value must be the type code of the attribute, e.g. "width",
 * "length", "color" or similar types. You can set the layout for all
 * attributes at once using e.g.
 *
 *  client/html/catalog/detail/basket/attribute/type = array(
 *      'width' => 'select',
 *      'color' => 'radio',
 *  )
 *
 * Similarly, you can set the layout type for a specific attribute only,
 * leaving the rest untouched:
 *
 *  client/html/catalog/detail/basket/attribute/type/color = radio
 *
 * Note: Up to 2015.10 this option was available as
 * client/html/catalog/detail/basket/attribute
 *
 * @param array List of attribute types as key and layout types as value, e.g. "select" or "radio"
 * @since 2015.04
 * @category Developer
 * @category User
 * @see client/html/catalog/detail/basket/selection/type
 */

?>
<ul class="selection">
<?php foreach( $this->get( 'attributeConfigItems', array() ) as $code => $attributes ) : ?>
<?php	$layout = $this->config( 'client/html/catalog/detail/basket/attribute/type/' . $code, 'select' ); ?>
	<li class="select-item <?php echo $enc->attr( $layout ) . ' ' . $enc->attr( $code ); ?>">
		<div class="select-name"><?php echo $enc->html( $this->translate( 'client/code', $code ) ); ?></div>
		<div class="select-value">
			<select class="select-list" name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrconfid' ) ) ); ?>">
				<option class="select-option" value=""><?php echo $enc->html( $this->translate( 'client', 'none' ) ); ?></option>
<?php	foreach( $attributes as $id => $attribute ) : ?>
				<option class="select-option" value="<?php echo $enc->attr( $id ); ?>">
<?php		$priceItems = $attribute->getRefItems( 'price', 'default', 'default' ); ?>
<?php		if( ( $priceItem = reset( $priceItems ) ) !== false ) : ?>
<?php			$value = $priceItem->getValue() + $priceItem->getCosts(); ?>
<?php			$currency = $this->translate( 'client/currency', $priceItem->getCurrencyId() ); ?>
<?php			/// Configurable product attribute name (%1$s) with sign (%4$s, +/-), price value (%2$s) and currency (%3$s) ?>
<?php			echo $enc->html( sprintf( $this->translate( 'client', '%1$s ( %4$s%2$s%3$s )' ), $attribute->getName(), $this->number( abs( $value ) ), $currency, ( $value < 0 ? '−' : '+' ) ), $enc::TRUST ); ?>
<?php		else : ?>
<?php			echo $enc->html( $attribute->getName(), $enc::TRUST ); ?>
<?php		endif; ?>
				</option>
<?php	endforeach; ?>
			</select>
		</div>
	</li>
<?php endforeach; ?>
</ul>
<ul class="selection">
<?php foreach( $this->get( 'attributeCustomItems', array() ) as $id => $attribute ) : ?>
	<li class="select-item <?php echo $enc->attr( $attribute->getCode() ); ?>">
		<div class="select-name"><?php echo $enc->html( $this->translate( 'client/code', $attribute->getType() ) ); ?></div>
		<div class="select-value">
			<input name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrcustid', $id ) ) ); ?>" type="text" value="" placeholder="<?php echo $enc->attr( $attribute->getName() ); ?>" />
		</div>
	</li>
<?php endforeach; ?>
</ul>
<?php foreach( $this->get( 'attributeHiddenItems', array() ) as $id => $attribute ) : ?>
<input type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrhideid', $id ) ) ); ?>" value="<?php echo $enc->attr( $id ); ?>" />
<?php endforeach; ?>
