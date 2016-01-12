<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

?>
<div class="product-item-bundle card panel">
	<div id="product-item-bundle" class="header card-header collapsed" role="tab" data-toggle="collapse" data-parent="#accordion" data-target="#product-item-bundle-data" aria-expanded="false" aria-controls="product-item-bundle-data">
		<?php echo $enc->html( $this->translate( 'admin', 'Bundles' ) ); ?>
	</div>
	<div id="product-item-bundle-data" class="item-bundle card-block panel-collapse collapse" role="tabpanel" aria-labelledby="product-item-bundle">
		<div class="col-lg-6">
			<table class="bundle-list table table-default">
				<thead>
					<tr>
						<th><?php echo $enc->html( $this->translate( 'admin', 'Products' ) ); ?></th>
						<th class="actions"><div class="btn btn-primary fa fa-plus"></div></th>
					</tr>
				</thead>
				<tbody>
<?php foreach( $this->get( 'bundleData/product.lists.id', array() ) as $idx => $id ) : ?>
					<tr>
						<td>
							<input class="item-listid" type="hidden" name="bundle[product.lists.id][]" value="<?php echo $enc->attr( $id ); ?>" />
							<input class="item-label" type="hidden" name="bundle[product.label][]" value="<?php echo $enc->attr( $this->get( 'bundleData/product.label/' . $idx ) ); ?>" />
							<select class="combobox item-refid" name="bundle[product.lists.refid][]">
								<option value="<?php echo $enc->attr( $this->get( 'bundleData/product.lists.refid/' . $idx ) ); ?>" ><?php echo $enc->html( $this->get( 'bundleData/product.label/' . $idx ) ); ?></option>
							</select>
						</td>
						<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
					</tr>
<?php endforeach; ?>
					<tr class="prototype">
						<td>
							<input class="item-listid" type="hidden" name="bundle[product.lists.id][]" value="" disabled="disabled" />
							<input class="item-label" type="hidden" name="bundle[product.label][]" value="" disabled="disabled" />
							<select class="combobox-prototype item-refid" name="bundle[product.id][]" disabled="disabled">
							</select>
						</td>
						<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
					</tr>
				</tbody>
			</table>
		</div>
<?php echo $this->get( 'bundleBody' ); ?>
	</div>
</div>
