import { registerBlockType } from "@wordpress/blocks";

import { __ } from "@wordpress/i18n";



registerBlockType('wp-museum/simple-object-callout-block', {
	title: __('Simple Object Callout'),
	icon: 'archive',
	category: 'widgets',
	attributes: {
		object_id: {
			type: 'string',
			default: null
		},
		display_title: {
			type: 'boolean',
			default: true
		},
		display_excerpt: {
			type: 'boolean',
			default: true
		},
		display_thumbnail: {
			type: 'boolean',
			default: true
		},
		link_to_object: {
			type: 'boolean',
			default: true
		},
		fields: {
			type: Object,
			default: {}
		},
		field_data: {
			type: Object,
			default: {}
		}
	},
	edit: ( { attributes, setAttributes } ) => {
		const {
			object_id,
			display_title,
			display_excerpt,
			display_thumbnail,
			link_to_object,
			fields,
			field_data
		} = attributes;
		const base_rest_path = '/wp-museum/v1/';

		function updateFields ( ) {
			const object_path = base_rest_path + 'all/' + object_id
			apiFetch( { path: object_path } ).then( result => {
				setAttributes( {
					object_title:	result.post_title,
					url:			result.link,
					post_type:		result.post_type,
					excerpt:		result.excerpt
				} );
				apiFetch(
					{ path: base_rest_path + 
							'object_custom/' + 
							attributes.post_type 
					}
				).then( result => {
					for ( let key in result ) {
						if ( typeof ( fields[key] ) === 'undefined') {
							fields[key] = ( result[key]['callout_default'] === '1' );
						}
					}
					setAttributes( { 
						fields : fields,
						field_data: result 
					} );
				} );
			} );
		}

		function onChangeObjectID ( content ) {
			setAttributes( { object_id: content } );
		}

		function onUpdateButton ( ) {
			if ( object_id ) {
				updateFields();
			}
		}

		function FieldsPanel ( ) {
			if ( 
				Object.keys(fields).length > 0 &&
				Object.keys(field_data).length === Object.keys(fields).length 
			) {
				let items = [];
				for ( let key in fields ) {
					items.push(
						<CheckboxControl
							label = { field_data[key]['name'] }
							checked = { fields[key] }
							onChange = { ( val ) => { 
								fields[key] = val;
								setAttributes( { fields: fields } );
							} }
						/>
					);
				}
				return [
					<PanelBody
						title = "Custom Fields"
						initialOpen = {true}
					>
						{ items }
					</PanelBody>
				];
			} else {
				updateFields();
				return null;
			}
		}

		function OptionsPanel ( ) {
			return [
				<PanelBody
					title = "Options"
					initialOpen = {true}
				>
					<CheckboxControl
						label = 'Display Title'
						checked = { display_title }
						onChange = { ( val ) => { setAttributes( { display_title: val } ) } }
					/>
					<CheckboxControl
						label = 'Display Excerpt'
						checked = { display_excerpt }
						onChange = { ( val ) => { setAttributes( { display_excerpt: val } ) } }
					/>
					<CheckboxControl
						label = 'Display Thumbnail'
						checked = { display_thumbnail }
						onChange = { ( val ) => { setAttributes( { display_thumbnail: val } ) } }
					/>
					<CheckboxControl
						label = 'Link to Object'
						checked = { display_thumbnail }
						onChange = { ( val ) => { setAttributes( { link_to_object: val } ) } }
					/>
				</PanelBody>
			]
		}

		function CalloutContent ( ) {
			return null;
		}

		if ( object_id !== null && Object.keys( fields ).length === 0 ) {
			updateFields();
		}

		return [
			<>
				<InspectorControls>
					<PanelBody
						title = "Embed Object"
						initialOpen = {true}
					>
						<PanelRow>
							<TextControl
								label = 'Object ID'
								onChange = { onChangeObjectID }
								value = { object_id }
							>
							</TextControl>
						</PanelRow>
						<PanelRow>
							<Button isDefault isPrimary
								onClick = { onUpdateButton }
							>
								Update
							</Button>
							<ObjectSearchButton>
								Search
							</ObjectSearchButton>
						</PanelRow>
					</PanelBody>
					<OptionsPanel />
					<FieldsPanel />
				</InspectorControls>
				<CalloutContent />
			</>
			
		];
	},
	save: ( props ) => {

	}
});