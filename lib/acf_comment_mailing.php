<?php

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_5fd7ab45b323f',
	'title' => __('Nastavení mailingu komentářů', 'shp-partneri'),
	'fields' => array(
		array(
			'key' => 'field_5fd7ab509d485',
			'label' => __('E-mail s upozorněním o nově autorizovaném hodnocení', 'shp-partneri'),
			'name' => 'authorized_review_email_enabled',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => 'E-mail aktivován',
			'default_value' => 0,
			'ui' => 0,
			'ui_on_text' => '',
			'ui_off_text' => '',
		),
    array(
			'key' => 'field_5ca5e9b129968',
			'label' => __( 'Příjemce e-mailu s upozorněním o nově autorizovaném hodnocení', 'shp-partneri' ),
			'name' => 'authorized_review_email_recipient',
			'type' => 'email',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'comments-mailing',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
));

endif;