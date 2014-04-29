<?php 
	$cart = SEC_Cart::get_instance();
	$total = $cart->get_total(); ?>
<script type="text/javascript" charset="utf-8">
	jQuery(document).ready(function($){
		jQuery("#gb_credit_affiliate_credits").live('keyup', function() {
			show_hide_payment_options( $(this).val() );
		});
		jQuery("#gb_credit_account_balance").live('keyup', function() {
			show_hide_payment_options( $(this).val() );
		});
		var show_hide_payment_options = function( val ) {
			var total = <?php echo $total ?>;
			if ( val >= total ) {
				jQuery('.payment_method').slideUp();
				jQuery('.gb_credit_card_field_wrap').slideUp();
			} 
			else {
				jQuery('.payment_method').fadeIn();
				jQuery('.gb_credit_card_field_wrap').slideDown();
			};
		};
	});
</script>

<div class="checkout_block clearfix">

	<div class="paymentform-info">
		<legend class="section_heading"><?php sec_e( 'Payment Information' ); ?></legend>
	</div>

	<?php foreach ( $fields as $key => $data ): ?>
		<?php if ( $data['weight'] < 1 && !in_array( $key, array( 'cc_name', 'cc_number', 'cc_expiration_month', 'cc_expiration_year', 'cc_cvv' ) ) ): ?>
				<div class="form-group <?php echo $key ?>">
					<?php if ( $data['type'] == 'heading' ): ?>
						<legend class="legend form-heading" ><?php echo $data['label']; ?></legend>
					<?php elseif ( $data['type'] != 'checkbox' ): ?>
						<span class="sr-only label_wrap"><?php gb_form_label( $key, $data, 'credit' ); ?></span>
						<div class="input_wrap"><?php gb_form_field( $key, $data, 'credit' ); ?></div>
					<?php else: ?>
						<div class="checkbox input_wrap">
							<label for="gb_credit_<?php echo $key; ?>">
								<?php
									// add class by modifying the attributes.
									$data['attributes']['class'][] = 'checkbox'; ?>
								<?php gb_form_field( $key, $data, 'credit' ); ?> <?php echo $data['label']; ?>
							</label>
						</div>
					<?php endif; ?>
				</div>
		<?php endif; ?>
	<?php endforeach; ?>
	<div class="gb_credit_card_field_wrap">
		<div class="row">
			<?php if ( $fields['cc_number'] ): ?>
				<div class="form-group col-sm-7">
					<span class="sr-only label_wrap"><?php gb_form_label( 'cc_number', $fields['cc_number'], 'credit' ); ?></span>
					<div class="input_wrap"><?php gb_form_field( 'cc_number', $fields['cc_number'], 'credit' ); ?></div>
				</div>
			<?php endif; ?>
		</div>
		<div class="row">
			<?php if ( $fields['cc_name'] ): ?>
				<div class="form-group col-sm-5">
					<span class="sr-only label_wrap"><?php gb_form_label( 'cc_name', $fields['cc_name'], 'credit' ); ?></span>
					<div class="input_wrap"><?php gb_form_field( 'cc_name', $fields['cc_name'], 'credit' ); ?></div>
				</div>
			<?php endif; ?>
			<?php if ( $fields['cc_cvv'] ): ?>
				<div class="form-group col-sm-2">
					<span class="sr-only label_wrap"><?php gb_form_label( 'cc_cvv', $fields['cc_cvv'], 'credit' ); ?></span>
					<div class="input_wrap"><?php gb_form_field( 'cc_cvv', $fields['cc_cvv'], 'credit' ); ?></div>
				</div>
			<?php endif; ?>
			<?php if ( $fields['cc_expiration_month'] && $fields['cc_expiration_year'] ): ?>
				<div class="form-group col-sm-5">
					<span class="sr-only label_wrap">
						<?php gb_form_label( 'cc_expiration_year', $fields['cc_expiration_year'], 'credit' ); ?>
					</span>
					<div class="input_wrap">
						<?php gb_form_field( 'cc_expiration_month', $fields['cc_expiration_month'], 'credit' ); ?>
						<?php gb_form_field( 'cc_expiration_year', $fields['cc_expiration_year'], 'credit' ); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php foreach ( $fields as $key => $data ): ?>
			<?php if ( $data['weight'] > 1 && !in_array( $key, array( 'cc_name', 'cc_number', 'cc_expiration_month', 'cc_expiration_year', 'cc_cvv' ) ) ): ?>
				<div class="form-group">
					<?php if ( $data['type'] == 'heading' ): ?>
						<legend class="legend form-heading" ><?php echo $data['label']; ?></legend>
					<?php elseif ( $data['type'] != 'checkbox' ): ?>
						<span class="sr-only label_wrap"><?php gb_form_label( $key, $data, 'credit' ); ?></span>
						<div class="input_wrap"><?php gb_form_field( $key, $data, 'credit' ); ?></div>
					<?php else: ?>
						<div class="checkbox input_wrap">
							<label for="gb_credit_<?php echo $key; ?>">
								<?php
									// add class by modifying the attributes.
									$data['attributes']['class'][] = 'checkbox'; ?>
								<?php gb_form_field( $key, $data, 'credit' ); ?> <?php echo $data['label']; ?>
							</label>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>
<?php if ( is_user_logged_in() ): ?>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		var cc_ids = '#gb_credit_cc_name,#gb_credit_cc_number,#gb_credit_cc_expiration_year,#gb_credit_cc_expiration_month,#gb_credit_cc_cvv'
		add_cc_required();
	    if ( ( $('#gb_credit_account_balance').val() != '0' || $('#gb_credit_affiliate_credits').val() ) != '0' ) {
	        remove_cc_required();
	    };
	    $('#gb_credit_account_balance, #gb_credit_affiliate_credits').on('keyup', function(){
			var value = $(this).val();
			if ( value ) {
				remove_cc_required();
			};
	    });
	    
	    check_payment_option();
		$('input[name="gb_credit_payment_method"]').change( function() {
			check_payment_option();
		});

		function check_payment_option() {
			var value = $('input[name="gb_credit_payment_method"]').val();
			if ( value != 'credit' ) {
				remove_cc_required();
			};
		}
	    function add_cc_required() {
	        $(cc_ids).attr('required','');
	    }
	    function remove_cc_required() {
	        $(cc_ids).removeAttr('required');
	    }
	});
	</script>
<?php endif;