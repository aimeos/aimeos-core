<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

?>
<div class="product-item-bundle card panel">
	<div id="product-item-bundle" class="header card-header collapsed" role="tab" data-toggle="collapse" data-parent="#accordion" href="#product-item-bundle-data" aria-expanded="false" aria-controls="product-item-bundle-data">
		<?php echo $enc->html( $this->translate( 'admin/jqadm', 'Bundles' ) ); ?>
	</div>
	<div id="product-item-bundle-data" class="item-bundle card-block panel-collapse collapse" role="tabpanel" aria-labelledby="product-item-bundle">
		<div class="col-lg-6">
			<table class="bundle-list table table-default">
				<thead>
					<tr>
						<th><?php echo $enc->html( $this->translate( 'admin/jqadm', 'Bundled products' ) ); ?></th>
						<th class="actions"><div class="btn btn-primary fa fa-plus"></div></th>
					</tr>
				</thead>
				<tbody>
<?php foreach( $this->get( 'bundleItems', array() ) as $id => $item ) : ?>
					<tr>
						<td>
							<select class="combobox" name="bundle[product.id][]">
								<option value="<?php echo $enc->attr( $id ); ?>" ><?php echo $enc->html( $item->getLabel() ); ?></option>
							</select>
						</td>
						<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
					</tr>
<?php endforeach; ?>
					<tr class="prototype">
						<td>
							<select class="combobox-prototype" name="bundle[product.id][]" disabled="disabled">
							</select>
						</td>
						<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
