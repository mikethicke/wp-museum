import {
	InspectorControls
} from '@wordpress/blockEditor'

import { 
	PanelBody,
	PanelRow,
	TextControl,
	ButtonGroup,
	Button,
	CheckboxControl,
	SelectControl,
	Dashicon,
	RangeControl,
	ColorPicker
} from '@wordpress/components';

import {
	Component
} from '@wordpress/element';

import { __ } from "@wordpress/i18n";

import apiFetch from '@wordpress/api-fetch';

import ObjectSearchButton from '../components/object-search-box';
import InfoContent from './info-content';
import InfoPlaceholder from './info-placeholder';

const imageSizes = {
	thumbnail: { height: 150, width: 150 },
	medium: { height: 300, width: 300 },
	large: { height: 1024, width: 1024 },
	full: { height: null, width: null }
}

class AppearancePanel extends Component {
	constructor ( props ) {
		super ( props );
		this.setAppearance = this.setAppearance.bind( this );
		this.render = this.render.bind( this );
	}

	setAppearance ( field, val ) {
		const { appearance, setAttributes } = this.props;
		let newVal;
		val ? newVal = val : newVal = 0;
		const newAppearance = Object.assign( {}, appearance );
		if ( field === 'borderColor' || field === 'backgroundColor' ) {
			newVal = newVal.hex;
		}
		newAppearance[ field ] = newVal;
		setAttributes( { appearance: newAppearance } )
	}
	
	render ( ) {
		const { appearance, setAttributes } = this.props;
		const { borderWidth, borderColor, backgroundColor, backgroundOpacity } = appearance;

		return [
			<PanelBody
				title = "Appearance"
				initialOpen = { false }
			>
				<PanelRow>
					<RangeControl
						label = 'Border Width'
						allowReset
						initialPosition = '0'
						onChange = { ( val ) => this.setAppearance( 'borderWidth', val ) }
						min = '0'
						max = '5'
						step = '0.5'
						value = { borderWidth }
					/>
				</PanelRow>
				<PanelRow>
					<p>Border Color</p>
					<ColorPicker
						color = { borderColor }
						onChangeComplete = { ( val ) => this.setAppearance( 'borderColor', val ) }
						disableAlpha
					/>
				</PanelRow>
				<PanelRow>
					<p>Background Color</p>
					<ColorPicker
						color = { backgroundColor }
						onChangeComplete = { ( val ) => this.setAppearance( 'backgroundColor', val ) }
						disableAlpha
					/>
				</PanelRow>
				<PanelRow>
					<RangeControl
						label = 'Background Opacity'
						allowReset
						initialPosition = '0'
						onChange = { ( val ) => this.setAppearance( 'backgroundOpacity', val ) }
						min = '0'
						max = '1'
						step = '0.01'
						value = { backgroundOpacity }
					/>
				</PanelRow>
			</PanelBody>
		];
	}
}

class FieldsPanel extends Component {

	updateField ( key, val ) {
		const { setAttributes, toggle, fields } = this.props;
		fields[key] = val;
		setAttributes ( { 
			fields: fields,
			toggle: ! toggle
		} );
	}

	render () {
		const { fieldData, fields } = this.props;
		if ( 
			Object.keys(fields).length > 0 &&
			Object.keys(fieldData).length === Object.keys(fields).length 
		) {
			let items = [];
			for ( let key in fields ) {
				items.push( //Use map instead
					<CheckboxControl
						key = { key.toString() }
						label = { fieldData[key]['name'] }
						checked = { fields[key] }
						onChange = { ( val ) => { this.updateField( key, val ) } }
					/>
				);
			}
			return [
				<PanelBody
					title = "Custom Fields"
					initialOpen = { false }
				>
					{ items }
				</PanelBody>
			];
		} else {
			return null;
		}
	}
}

class OptionsPanel extends Component {
	render () {
		const { attributes, setAttributes } = this.props;
		const { displayTitle, displayExcerpt, displayThumbnail, linkToObject } = attributes;
		return [
			<PanelBody
				title = "Options"
				initialOpen = {true}
			>
				<CheckboxControl
					label = 'Display Title'
					checked = { displayTitle }
					onChange = { ( val ) => { setAttributes( { displayTitle: val } ) } }
				/>
				<CheckboxControl
					label = 'Display Excerpt'
					checked = { displayExcerpt }
					onChange = { ( val ) => { setAttributes( { displayExcerpt: val } ) } }
				/>
				<CheckboxControl
					label = 'Display Thumbnail'
					checked = { displayThumbnail }
					onChange = { ( val ) => { setAttributes( { displayThumbnail: val } ) } }
				/>
				<CheckboxControl
					label = 'Link to Object'
					checked = { linkToObject }
					onChange = { ( val ) => { setAttributes( { linkToObject: val } ) } }
				/>
			</PanelBody>
		]
	}
}

class ImageSizePanel extends Component {
	constructor ( props ) {
		super( props );

		this.updateImage = this.updateImage.bind( this );
		this.updateHeight = this.updateHeight.bind( this );
		this.updateWidth = this.updateWidth.bind( this );
		this.updateImageAlignment = this.updateImageAlignment.bind( this );
	}
	
	updateImage ( size ) {
		const { setAttributes, state } = this.props;
		const { imgHeight, imgWidth, imgReady } = state;

		if ( imgReady ) {
			const targetSize = imageSizes[ size ].width; //width == height
			let scaleFactor;
			if ( targetSize === null ) {
				scaleFactor = 1;
			} else {
				scaleFactor = targetSize / Math.max( imgWidth, imgHeight );
			}
			const newImageDimensions = {
				height: Math.round( scaleFactor * imgHeight ),
				width: Math.round( scaleFactor * imgWidth ),
				size: size
			};
			setAttributes ( {
				imageDimensions: newImageDimensions
			} );
		}	
	}

	updateHeight ( newHeight ) {
		const { setAttributes, state } = this.props;
		const { imgHeight, imgWidth, imgReady } = state;

		if ( imgReady ) {
			const setHeight = Math.min( newHeight, imgHeight );
			const setWidth = Math.round( setHeight / imgHeight * imgWidth )
			const newImageDimensions = {
				height: setHeight,
				width: setWidth,
				size: null
			};
			setAttributes ( {
				imageDimensions: newImageDimensions
			} );
		}	
	}

	updateWidth ( newWidth ) {
		const { setAttributes, state } = this.props;
		const { imgHeight, imgWidth, imgReady } = state;

		if ( imgReady ) {
			const setWidth = Math.min( newWidth, imgWidth);
			const setHeight = Math.round( setWidth / imgWidth * imgHeight )
			const newImageDimensions = {
				height: setHeight,
				width: setWidth,
				size: null
			};
			setAttributes ( {
				imageDimensions: newImageDimensions
			} );
		}	
	}

	updateImageAlignment ( newAlignment ) {
		const { setAttributes } = this.props;

		setAttributes( { imageAlignment: newAlignment } ); 
	}
	
	render () {
		const { attributes } = this.props;
		const { imageDimensions, imageAlignment } = attributes;
		const { width, height, size } = imageDimensions;

		const imageSizeOptions = [
			{ value: 'thumbnail', label: __( 'Thumbnail' ) },
			{ value: 'medium', label: __( 'Medium' ) },
			{ value: 'large', label: __( 'Large' ) },
			{ value: 'full', label: __( 'Full Size' ) },
		]

		return [
			<PanelBody
				title = { __( 'Image Settings' ) }
				initialOpen = { true }
			>
				<SelectControl
					label = { __( 'Image Size' ) }
					value = { size }
					options = { imageSizeOptions }
					onChange = { this.updateImage }
				/>
				<div>
					<p>{ __( 'Image Dimensions' ) }</p>
					<TextControl
						type="number"
						label={ __( 'Width' ) }
						value={ width || '' }
						min={ 1 }
						onChange={ this.updateWidth }
					/>
					<TextControl
						type="number"
						label={ __( 'Height' ) }
						value={ height || '' }
						min={ 1 }
						onChange={ this.updateHeight }
					/>
				</div>
				<div>
					<p>{ __( 'Image Alignment' ) }</p>
					<ButtonGroup>
						<Button
							isPrimary = { imageAlignment === 'left' }
							onClick = { () => { this.updateImageAlignment( 'left' ) } }
						>
							<Dashicon icon='align-left'/>
						</Button>
						<Button
							isPrimary = { imageAlignment === 'center' }
							onClick = { () => { this.updateImageAlignment( 'center' ) } }
						>
							<Dashicon icon='align-center'/>
						</Button>
						<Button
							isPrimary = { imageAlignment === 'right' }
							onClick = { () => { this.updateImageAlignment( 'right' ) } }
						>
							<Dashicon icon='align-right'/>
						</Button>
					</ButtonGroup>
				</div>
				

			</PanelBody>
		]
	}
}

class FontSizePanel extends Component {

	render ( ) {
		const { setAttributes, titleTag, fontSize } = this.props;

		const titleTagOptions = [
			{ label: 'Heading 2', value: 'h2' },
			{ label: 'Heading 3', value: 'h3' },
			{ label: 'Heading 4', value: 'h4' },
			{ label: 'Heading 5', value: 'h5' },
			{ label: 'Heading 6', value: 'h6' },
			{ label: 'Paragraph', value: 'p' },
		];

		return [
			<PanelBody
				title = "Font Size"
				initialOpen = { false }
			>
				<PanelRow>
					<SelectControl
						label = 'Title Style'
						value = { titleTag }
						options = { titleTagOptions }
						onChange = { ( val ) => setAttributes( { titleTag: val } ) }
					/>
				</PanelRow>
				<PanelRow>
					<RangeControl
						label = 'Text (em)'
						onChange = { ( val ) => val ? setAttributes( { fontSize: val } ) : setAttributes( { fontSize: 1 } ) }
						min = '0.25'
						max = '2'
						step = '0.05'
						value = { fontSize }
						initialPosition = '1'
						withInputField
						allowReset
					/>
				</PanelRow>
			</PanelBody>
		];
	}
}

function EditContent ( props ) {
	const { attributes, state, onChangeObjectID, onUpdateButton, imageSizes } = props;
	const { 
		objectID,
		title,
		excerpt,
		thumbnailURL,
		objectURL,
		fields,
		fieldData,
		imageDimensions,
		imageAlignment,
		fontSize,
		displayTitle,
		displayThumbnail,
		displayExcerpt,
		linkToObject,
		appearance,
		titleTag
	} = attributes;
	const {
		object_fetched
	} = state;

	if ( object_fetched ) {
		return [
			<InfoContent 
				objectID = { objectID }
				title = { displayTitle ? title : null }
				excerpt = { displayExcerpt ? excerpt : null }
				thumbnailURL = { displayThumbnail ? thumbnailURL : null }
				objectURL = { linkToObject ? objectURL : null }
				fields = { fields }
				fieldData = { fieldData }
				imageDimensions = { imageDimensions }
				imageSizes = { imageSizes }
				state = { state }
				imageAlignment = { imageAlignment }
				fontSize = { fontSize }
				appearance = { appearance }
				titleTag = { titleTag }
			/>
		];
	} else {
		return [
			<InfoPlaceholder
				objectID = { objectID }
				onChangeObjectID = { onChangeObjectID }
				onUpdateButton = { onUpdateButton }
			/>
		]
	}
}

class ObjectInfoEdit extends Component {
	constructor ( props ) {
		super ( props );

		this.onUpdateButton = this.onUpdateButton.bind( this );
		this.onChangeObjectID = this.onChangeObjectID.bind( this );
		this.fetchFieldData = this.fetchFieldData.bind ( this );

		this.state = {
			object_fetched: false,
			object_data: {},
			imgHeight: null,
			imgWidth: null,
			imgReady: false
		}
	}
	
	getImageDimensions ( ) {
		const { thumbnailURL } = this.props.attributes;
		const that = this;

		// https://stackoverflow.com/questions/52059596/loading-an-image-on-web-browser-using-promise
		function loadImage(src) {
			return new Promise( (resolve, reject) => {
				const img = new Image();
				img.addEventListener("load", () => resolve(img));
				img.addEventListener("error", err => reject(err));
				img.src = src;
			} );
		};
		loadImage( thumbnailURL ).then( img => {
			that.setState( {
				imgHeight: img.height,
				imgWidth: img.width,
				imgReady: true
			} );
		} );
	}
	
	fetchFieldData ( ) {
		const { setAttributes } = this.props;
		const { objectID } = this.props.attributes;
		const base_rest_path = '/wp-museum/v1/';

		if ( objectID != null ) {
			const object_path = base_rest_path + 'all/' + objectID;
			const that = this;
			apiFetch( { path: object_path } ).then( result => {
				that.setState( { object_data: result } );
				setAttributes( {
					title: result['post_title'],
					excerpt: result['excerpt'],
					thumbnailURL: result['thumbnail'][0],
					objectURL: result['link']
				} );
				if ( that.props.attributes.thumbnailURL != null ) {
					that.setState( { imgReady: false } );
					that.getImageDimensions();
				}
				apiFetch(
					{ path: base_rest_path + 
							result.post_type +
							'/custom'
					}
				).then( result => {
					const { fields } = that.props.attributes;
					const { object_data } = that.state;
					let newFields = {};
					let fieldData = {};
					for ( let key in result ) {
						if ( typeof ( fields[key] ) === 'undefined') {
							newFields[key] = false;
						} else {
							newFields[key] = fields[key];
						}
						let content = '';
						if ( result[key]['type'] === 'tinyint' ) {
							if ( object_data[ result[key]['slug'] ] === 1 ) {
								content = 'Yes';
							} else {
								content = 'No';
							}
						} else {
							content = object_data[ result[key]['slug'] ];
						}
						fieldData[key] = {
							name: result[key]['name'],
							content: content
						}
					}
					setAttributes( {
						fields: newFields,
						fieldData: fieldData
					} );
					that.setState( { 
						object_fetched: true
					} ); 
				} );
			} );
		}
	}

	componentDidMount() {
		this.fetchFieldData();
	}

	onChangeObjectID( content ) {
		const { setAttributes } = this.props;

		setAttributes( { objectID: content } );
	}

	onUpdateButton() {
		this.fetchFieldData();
	}
	
	render () {
		const { setAttributes, attributes } = this.props;
		const { fontSize, appearance, titleTag } = attributes;
		
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
								onChange = { this.onChangeObjectID }
								value = { this.objectID }
							/>
						</PanelRow>
						<PanelRow>
							<Button isDefault isPrimary
								onClick = { this.onUpdateButton }
							>
								Update
							</Button>
							<ObjectSearchButton>
								Search
							</ObjectSearchButton>
						</PanelRow>
					</PanelBody>
					<OptionsPanel { ...this.props } />
					<ImageSizePanel { ...this.props }
						state = { this.state }
					/>
					<AppearancePanel
						setAttributes = { setAttributes }
						appearance = { appearance }
					/>
					<FontSizePanel
						setAttributes = { setAttributes }
						titleTag = { titleTag }
						fontSize = { fontSize }
					/>
					<FieldsPanel
						setAttributes = { this.props.setAttributes }
						fields = { this.props.attributes.fields }
						fieldData = { this.props.attributes.fieldData }
						toggle = { this.props.attributes.toggle }
					/>
				</InspectorControls>
				<EditContent { ...this.props } 
					onUpdateButton = { this.onUpdateButton }
					onChangeObjectID = { this.onChangeObjectID }
					state = { this.state }
					imageSizes = { imageSizes }
				/>
			</>	
		];
	}
}

export default ObjectInfoEdit;