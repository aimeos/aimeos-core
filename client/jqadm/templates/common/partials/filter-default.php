<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

$attributes = $this->get( 'filterAttributes', array() );
$operators = $this->get( 'filterOperators', array() );
$operators = ( isset( $operators['compare'] ) ? $operators['compare'] : array() );

$filter = $this->param( 'filter' );

?>
<table>
<?php foreach( (array) $filter['key'] as $pos => $key ) : ?>
	<tr class="input-group filter-line">
		<td>
			<div class="glyphicon glyphicon-plus" aria-label="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'Add filter' ) ); ?>"></div>
		</td>
		<td>
			<fieldset>
				<select name="filter[key][]" class="filter-key form-control">
<?php foreach( $attributes as $code => $attr ) : ?>
					<option value="<?php echo $enc->attr( $code ); ?>" <?php echo ( $code === $key ? 'selected' : '' ); ?>><?php echo $enc->html( $attr->getLabel() ); ?></option>
<?php endforeach; ?>
				</select><!--
				--><select name="filter[op][]" class="filter-operator form-control">
<?php foreach( $operators as $code ) : ?>
					<option value="<?php echo $enc->attr( $code ); ?>" <?php echo ( $code === $filter['op'][$pos] ? 'selected' : '' ); ?>><?php echo $enc->html( $this->translate( 'client/jqadm/code', $code ) ); ?></option>
<?php endforeach; ?>
				</select><!--
				--><input name="filter[val][]" class="filter-value form-control" type="text" value="<?php echo $enc->attr( ( isset( $filter['val'][$pos] ) ? $filter['val'][$pos] : '' ) ); ?>" />
			</fieldset>
		</td>
	</tr>
<?php endforeach; ?>
	<tr class="input-group filter-line">
		<td>
			<div class="glyphicon glyphicon-plus" aria-label="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'Add filter' ) ); ?>"></div>
		</td>
		<td>
			<fieldset>
				<select name="filter[key][]" class="filter-key form-control">
<?php foreach( $attributes as $code => $attr ) : ?>
					<option value="<?php echo $enc->attr( $code ); ?>"><?php echo $enc->html( $attr->getLabel() ); ?></option>
<?php endforeach; ?>
				</select><!--
				--><select name="filter[op][]" class="filter-operator form-control">
<?php foreach( $operators as $code ) : ?>
					<option value="<?php echo $enc->attr( $code ); ?>"><?php echo $enc->html( $this->translate( 'client/jqadm/code', $code ) ); ?></option>
<?php endforeach; ?>
				</select><!--
				--><input name="filter[val][]" class="filter-value form-control" type="text" />
			</fieldset>
		</td>
	</tr>
	<tr class="input-group prototype">
		<td>
			<div class="glyphicon glyphicon-plus" aria-label="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'Add filter' ) ); ?>"></div>
		</td>
		<td>
			<fieldset>
				<select name="filter[key][]" class="filter-key form-control">
<?php foreach( $attributes as $code => $attr ) : ?>
<?php	if( $attr->isPublic() ) : ?>
					<option value="<?php echo $enc->attr( $code ); ?>"><?php echo $enc->html( $attr->getLabel() ); ?></option>
<?php	endif; ?>
<?php endforeach; ?>
				</select><!--
				--><select name="filter[op][]" class="filter-operator form-control">
<?php foreach( $operators as $code ) : ?>
					<option value="<?php echo $enc->attr( $code ); ?>"><?php echo $enc->html( $this->translate( 'client/jqadm/code', $code ) ); ?></option>
<?php endforeach; ?>
				</select><!--
				--><input name="filter[val][]" class="filter-value form-control" type="text" />
			</fieldset>
		</td>
	</tr>
	<tr class="buttons-action">
		<td colspan="2">
			<button class="btn btn-primary"><?php echo $this->translate( 'client/jqadm', 'Search' ); ?></button>
			<a class="btn btn-warning" href="#"><?php echo $this->translate( 'client/jqadm', 'Clear' ); ?></a>
		</td>
	</tr>
</table>
