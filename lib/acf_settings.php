<?php

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array (
	'key' => 'group_59cab5068ad1b',
	'title' => 'Kategorie',
	'fields' => array (
		array (
			'key' => 'field_59db64355c03c',
			'label' => 'Titulek',
			'name' => 'title',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array (
			'key' => 'field_59cab50932231',
			'label' => 'Obrázek',
			'name' => 'image',
			'type' => 'image',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'array',
			'preview_size' => 'medium',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'taxonomy',
				'operator' => '==',
				'value' => 'category_professionals',
			),
		),
		array (
			array (
				'param' => 'taxonomy',
				'operator' => '==',
				'value' => 'category_plugins',
			),
		),
		array (
			array (
				'param' => 'taxonomy',
				'operator' => '==',
				'value' => 'category_tools',
			),
		),
		array (
			array (
				'param' => 'taxonomy',
				'operator' => '==',
				'value' => 'category_requests',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_59e8b759957bd',
	'title' => 'Nastavení napojení',
	'fields' => array (
		array (
			'key' => 'field_59f20b6428ac3',
			'label' => 'Perex na domovské stránce',
			'name' => 'perexPlugin',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'rows' => 3,
			'new_lines' => 'wpautop',
		),
		array (
			'key' => 'field_59e8b7599874e',
			'label' => 'Nadpis popisu',
			'name' => 'titleDescriptionPlugin',
			'type' => 'text',
			'instructions' => 'Bude zobrazeno jako nadpis popisu příspěvku.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array (
			'key' => 'field_59e8b759987e1',
			'label' => 'Nadpis přínosu',
			'name' => 'titleBenefitPlugin',
			'type' => 'text',
			'instructions' => 'Bude zobrazeno jako nadpis přínosu příspěvku.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'plugin-settings',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'seamless',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_59e8b782acbbd',
	'title' => 'Nastavení nástrojů',
	'fields' => array (
		array (
			'key' => 'field_59f20b48e40ac',
			'label' => 'Perex na domovské stránce',
			'name' => 'perexTool',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'rows' => 3,
			'new_lines' => 'wpautop',
		),
		array (
			'key' => 'field_59e8b782af9a3',
			'label' => 'Nadpis popisu',
			'name' => 'titleDescriptionTool',
			'type' => 'text',
			'instructions' => 'Bude zobrazeno jako nadpis popisu příspěvku.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array (
			'key' => 'field_59e8b782afa4a',
			'label' => 'Nadpis přínosu',
			'name' => 'titleBenefitTool',
			'type' => 'text',
			'instructions' => 'Bude zobrazeno jako nadpis přínosu příspěvku.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'tool-settings',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'seamless',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_59e8b36e766a4',
	'title' => 'Nastavení profesionálů',
	'fields' => array (
		array (
			'key' => 'field_59f20a6f1b99c',
			'label' => 'Perex na domovské stránce',
			'name' => 'perexProfessional',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'rows' => 3,
			'new_lines' => 'wpautop',
		),
		array (
			'key' => 'field_59e8b58c2df87',
			'label' => 'Nadpis popisu',
			'name' => 'titleDescriptionProfessional',
			'type' => 'text',
			'instructions' => 'Bude zobrazeno jako nadpis popisu příspěvku.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array (
			'key' => 'field_59e8b5d82df88',
			'label' => 'Nadpis přínosu',
			'name' => 'titleBenefitProfessional',
			'type' => 'text',
			'instructions' => 'Bude zobrazeno jako nadpis přínosu příspěvku.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'professional-settings',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'seamless',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_59f7359232f8a',
	'title' => 'Patička',
	'fields' => array (
		array (
			'key' => 'field_59f735a17add5',
			'label' => 'Text v patičce',
			'name' => 'themeFooterText',
			'type' => 'wysiwyg',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'tabs' => 'all',
			'toolbar' => 'basic',
			'media_upload' => 0,
			'delay' => 0,
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'acf-options-paticka',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'seamless',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_59fccbc35c759',
	'title' => 'Domovská stránka',
	'fields' => array (
		array (
			'key' => 'field_59fccbc35fc39',
			'label' => 'Hero text',
			'name' => 'themeHomepageHeroText',
			'type' => 'text',
			'instructions' => 'Text bude použit v hero boxu a meta tagu description domovské stánky. Pro zobrazení celkového počtu profilů vložte: %count%',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'acf-options-obecne',
			),
		),
	),
	'menu_order' => 5,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_5a006f0334694',
	'title' => 'Nastavení',
	'fields' => array (
		array (
			'key' => 'field_5a006f06b51ca',
			'label' => 'Google Tag Manager ID',
			'name' => 'themeGTMId',
			'type' => 'text',
			'instructions' => 'Ve tvaru GTM-XXXX',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'acf-options-nastaveni',
			),
		),
	),
	'menu_order' => 5,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_5a0355b835bd6',
	'title' => 'Open graph',
	'fields' => array (
		array (
			'key' => 'field_5a0355c7459b1',
			'label' => 'Open graph obrázek',
			'name' => 'themeOpenGraphImage',
			'type' => 'image',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'array',
			'preview_size' => 'medium',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'acf-options-obecne',
			),
		),
	),
	'menu_order' => 7,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_59f202ab3988a',
	'title' => 'Reference',
	'fields' => array (
		array (
			'key' => 'field_59f208137ad64',
			'label' => 'Zobrazit na domovské stránce',
			'name' => 'themeReferenceShowHomepage',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => 'Zobrazit',
			'default_value' => 0,
			'ui' => 0,
			'ui_on_text' => '',
			'ui_off_text' => '',
		),
		array (
			'key' => 'field_59f202b7c2217',
			'label' => 'Jméno',
			'name' => 'themeReferenceName',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array (
			'key' => 'field_59f20310c2218',
			'label' => 'Pozice',
			'name' => 'themeReferencePosition',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array (
			'key' => 'field_59f20330c221a',
			'label' => 'Web',
			'name' => 'themeReferenceUrl',
			'type' => 'url',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
		),
		array (
			'key' => 'field_59f2055a15c9e',
			'label' => 'Fotka',
			'name' => 'themeReferenceImage',
			'type' => 'image',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'array',
			'preview_size' => 'thumbnail',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
		),
		array (
			'key' => 'field_59f20345c221b',
			'label' => 'Text',
			'name' => 'themeReferenceText',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'rows' => 5,
			'new_lines' => 'wpautop',
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'acf-options-obecne',
			),
		),
	),
	'menu_order' => 10,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_BaPbMgUbyKxHo',
	'title' => 'Staň se partnerem',
	'fields' => array (
		array (
			'key' => 'field_MItgCUEMLhWNJ',
			'label' => 'Titulek',
			'name' => 'themeBecomePartnerTitle',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
        array (
			'key' => 'field_zncNuZFoXb2Io',
			'label' => 'Text',
			'name' => 'themeBecomePartnerContent',
			'type' => 'wysiwyg',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
        array (
			'key' => 'field_IpkTta64keTt2',
			'label' => 'Ilustrační obrázek',
			'name' => 'themeBecomePartnerImage',
			'type' => 'image',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'array',
			'preview_size' => 'thumbnail',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
		),
        /*array (
			'key' => 'field_59f7532c4dude',
			'label' => 'Text na tlačítku',
			'name' => 'themeBecomePartnerButtonText',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),*/

	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'acf-options-obecne',
			),
		),
	),
	'menu_order' => 12,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_59fc7890159c2',
	'title' => 'Email pro žádost o zařazení mezi Shoptet partnery',
	'fields' => array (
		array (
			'key' => 'field_59fc78901902c',
			'label' => 'E-mail',
			'name' => 'themeCtaMailAddress',
			'type' => 'email',
			'instructions' => 'Adresa příjemce e-mailu',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
		),
		array (
			'key' => 'field_59fc789019067',
			'label' => 'Předmět',
			'name' => 'themeCtaMailSubject',
			'type' => 'text',
			'instructions' => 'Předvyplněný předmět e-mailu',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array (
			'key' => 'field_59fc78901909e',
			'label' => 'Text zprávy',
			'name' => 'themeCtaMailBody',
			'type' => 'textarea',
			'instructions' => 'Předvyplněný text e-mailu',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'rows' => 5,
			'new_lines' => 'br',
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'acf-options-obecne',
			),
		),
	),
	'menu_order' => 15,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_59fc799f9093c',
	'title' => 'Výzva k akci v seznamu',
	'fields' => array (
		array (
			'key' => 'field_59fc799f93b92',
			'label' => 'Zobrazit v seznamu profilů',
			'name' => 'themeCtaShowList',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => 'Zobrazit',
			'default_value' => 0,
			'ui' => 0,
			'ui_on_text' => '',
			'ui_off_text' => '',
		),
		array (
			'key' => 'field_59fc799f93c74',
			'label' => 'Titulek',
			'name' => 'themeListCtaTitle',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array (
			'key' => 'field_59fc799f93ce1',
			'label' => 'Text na tlačítku',
			'name' => 'themeListCtaButtonText',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'acf-options-obecne',
			),
		),
	),
	'menu_order' => 20,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));



acf_add_local_field_group(array (
	'key' => 'group_K4M9DdbzGs3Vt',
	'title' => 'Stížnost na partnera',
	'fields' => array (
        array (
			'key' => 'field_6XyApEcCt8z2b',
			'label' => 'Titulek',
			'name' => 'themeComplaintTitle',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
        array (
			'key' => 'field_2d6tuB9cdM4AC',
			'label' => 'Text',
			'name' => 'themeComplaintContent',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
        array (
			'key' => 'field_3d6tuC9cdM5AC',
			'label' => 'Kontaktní formulář (shortcode)',
			'name' => 'themeComplaintContact',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
        array (
			'key' => 'field_pU3bf7YGr38DC',
			'label' => 'Text u kontaktního formuláře',
			'name' => 'themeComplaintContactText',
			'type' => 'wysiwyg',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		/*array (
			'key' => 'field_HX3j467EbMUWc',
			'label' => 'Text na tlačítku',
			'name' => 'themeComplaintButtonText',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),*/
		/*array (
			'key' => 'field_7d2rBp3PxdLds',
			'label' => 'E-mail',
			'name' => 'themeComplaintMailAddress',
			'type' => 'email',
			'instructions' => 'Adresa příjemce e-mailu',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
		),*/
		/*array (
			'key' => 'field_pU4bc7YGr37DG',
			'label' => 'Předmět',
			'name' => 'themeComplaintMailSubject',
			'type' => 'text',
			'instructions' => 'Předvyplněný předmět e-mailu',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),*/
		/*array (
			'key' => 'field_x7sL9Ghtep3YV',
			'label' => 'Text zprávy',
			'name' => 'themeComplaintMailBody',
			'type' => 'textarea',
			'instructions' => 'Předvyplněný text e-mailu',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'rows' => 5,
			'new_lines' => 'br',
		),*/
	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'acf-options-obecne',
			),
		),
	),
	'menu_order' => 25,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_5a03324589bf8',
	'title' => 'Stránka 404',
	'fields' => array (
		array (
			'key' => 'field_5a033272c5fd0',
			'label' => 'Text',
			'name' => 'themeNotFoundText',
			'type' => 'wysiwyg',
			'instructions' => 'Tento text se zobrazí větším písmem pod nadpisem',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'tabs' => 'all',
			'toolbar' => 'basic',
			'media_upload' => 0,
			'delay' => 0,
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'acf-options-obecne',
			),
		),
	),
	'menu_order' => 30,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_59r74pffe3d26',
	'title' => 'Shoptet partneři - odznaky',
	'fields' => array (
		array (
			'key' => 'field_iLLf05wDuSG7A',
			'label' => 'Titulek',
			'name' => 'themeBadgesTitle',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
        array (
			'key' => 'field_nDTLivNyCdvWR',
			'label' => 'Text (nyní zakomentováno, nebude vidět)',
			'name' => 'themeBadgesContent',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array (
			'key' => 'field_e1HSLd0DJEVC4',
			'label' => 'Zlatý partner',
			'name' => 'themeBadgesGoldText',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
        array (
			'key' => 'field_XMzmZ28kWWx7x',
			'label' => 'Stříbrný partner',
			'name' => 'themeBadgesSilverText',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
        array (
			'key' => 'field_CG860wuZsx47X',
			'label' => 'Bronzový partner',
			'name' => 'themeBadgesBronzeText',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
	),

	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'acf-options-obecne',
			),
		),
	),
	'menu_order' => 35,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

acf_add_local_field_group(array (
	'key' => 'group_59r61pfbr3d26',
	'title' => 'Partnerský formulář',
	'fields' => array (
		array (
			'key' => 'field_iSSf05wMeSG7B',
			'label' => 'Formulář (shortcode)',
			'name' => 'themePartnerContactForm',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
	),

	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'acf-options-obecne',
			),
		),
	),
	'menu_order' => 40,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));




endif;