<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$selected = function( $key, $code ) {
	return ( $key == $code ? 'selected="selected"' : '' );
};

$enc = $this->encoder();

$target = $this->config( 'admin/jqadm/url/save/target' );
$cntl = $this->config( 'admin/jqadm/url/save/controller', 'jqadm' );
$action = $this->config( 'admin/jqadm/url/save/action', 'save' );
$config = $this->config( 'admin/jqadm/url/save/config', array() );

$listTarget = $this->config( 'admin/jqadm/url/search/target' );
$listCntl = $this->config( 'admin/jqadm/url/search/controller', 'jqadm' );
$listAction = $this->config( 'admin/jqadm/url/search/action', 'search' );
$listConfig = $this->config( 'admin/jqadm/url/search/config', array() );

$params = $this->param();

?>
<div class="product-item-bundle card">
	<div class="header card-header" role="tab" id="product-item-bundle" role="button" data-toggle="collapse" data-parent="#accordion" href="#product-item-bundle-data" aria-expanded="true" aria-controls="product-item-bundle-data">
		<a role="button" data-toggle="collapse" data-parent="#accordion" href="#product-bundle-data" aria-expanded="true" aria-controls="product-bundle-data">
			<?php echo $enc->html( $this->translate( 'admin/jqadm', 'Bundles' ) ); ?>
		</a>
	</div>
	<div id="product-item-bundle-data" class="item-basic-bundle card-block panel-collapse collapse" role="tabpanel" aria-labelledby="product-item-bundle">
		<div class="col-sm-6">
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
							<select class="form-control combobox" name="bundle[product.id][]">
								<option value="<?php echo $enc->attr( $id ); ?>" ><?php echo $enc->html( $item->getLabel() ); ?></option>
							</select>
						</td>
						<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
					</tr>
<?php endforeach; ?>
					<tr class="prototype">
						<td>
							<select class="form-control combobox-prototype" name="bundle[id][]">
							</select>
						</td>
						<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
