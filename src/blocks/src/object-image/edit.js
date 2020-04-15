import {
	InspectorControls,
	RichText,
} from '@wordpress/blockEditor'

import {
	Component
} from '@wordpress/element';

import { 
	PanelBody,
	CheckboxControl,
} from '@wordpress/components';

import { __ } from "@wordpress/i18n";
import apiFetch from '@wordpress/api-fetch';

import {
	ObjectEmbedPanel,
	ObjectSearchButton
} from '../components/object-search-box';
import AppearancePanel from '../components/appearance-panel';
import ImageSizePanel from '../components/image-size-panel';
import ImageSelector from '../components/image-selector'
import FontSizePanel from '../components/font-size-panel';

const OptionsPanel = ( props ) => {
	const {
		setAttributes,
		displayTitle,
		displayCatID,
		displayCaption,
		linkToObject,
		initialOpen
	} = props;

	return (
		<PanelBody
			title = "Options"
			initialOpen = { initialOpen }
		>
			<CheckboxControl
				label = 'Display Title'
				checked = { displayTitle }
				onChange = { ( val ) => { setAttributes( { displayTitle: val } ) } }
			/>
			<CheckboxControl
				label = 'Display Catalog ID'
				checked = { displayCatID }
				onChange = { ( val ) => { setAttributes( { displayCatID: val } ) } }
			/>
			<CheckboxControl
				label = 'Display Caption'
				checked = { displayCaption }
				onChange = { ( val ) => { setAttributes( { displayCaption: val } ) } }
			/>
			<CheckboxControl
				label = 'Link to Object'
				checked = { linkToObject }
				onChange = { ( val ) => { setAttributes( { linkToObject: val } ) } }
			/>
		</PanelBody>
	);
}

class ObjectImageEdit extends Component {
	constructor ( props ) {
		super( props );

		this.onSearchModalReturn = this.onSearchModalReturn.bind( this );
		this.setImageReady       = this.setImageReady.bind( this );

		this.state = {
			imgReady: false,
		}
	}

	onSearchModalReturn( returnValue ) {
		const { setAttributes } = this.props;

		const base_rest_path = '/wp-museum/v1/';

		if ( returnValue != null ) {
			setAttributes( { 
				objectID  : returnValue,
				imgURL    : null,
				imgHeight : null,
				imgWidth  : null,
				imgIndex  : 0
			} );

			this.setState( {
				imgReady: false
			} );

			const object_path = base_rest_path + 'all/' + returnValue;
			apiFetch( { path: object_path } ).then( result => {
				setAttributes( {
					title     : result[ 'post_title' ],
					objectURL : result[ 'link' ],
					catID     : result[ result[ 'cat_field' ] ],
				} );
			} );
		}
	}

	setImageReady( isReady ) {
		if ( isReady !== this.state.imgReady ) {
			this.setState( {
				imgReady : isReady
			} );
		}
	}

	render() {
		const {
			attributes,
			setAttributes,
		} = this.props;

		const {
			title,
			catID,
			objectID,
			objectURL,
			appearance,
			imgHeight,
			imgWidth,
			imgDimensions,
			imgAlignment,
			imgIndex,
			totalImages,
			imgURL,
			displayTitle,
			displayCatID,
			displayCaption,
			linkToObject,
			captionText,
			titleTag,
			fontSize,
		} = attributes;

		const {
			imgReady
		} = this.state;

		const TitleTag = titleTag;

		return (
			<>
			<InspectorControls>
				<ObjectEmbedPanel
					onSearchModalReturn = { this.onSearchModalReturn }
					title               = { title }
					catID               = { catID }
					objectID            = { objectID }
					objectURL           = { objectURL }
					initialOpen         = { true }
				/>
				<OptionsPanel
					setAttributes  = { setAttributes }
					displayTitle   = { displayTitle }
					displayCatID   = { displayCatID }
					displayCaption = { displayCaption }
					linkToObject   = { linkToObject }
				/>
				<ImageSizePanel
					setAttributes = { setAttributes }
					imgHeight     = { imgHeight }
					imgWidth      = { imgWidth }
					imgReady      = { imgReady }
					imgDimensions = { imgDimensions }
					imgAlignment  = { imgAlignment }
					initialOpen   = { true }
				/>
				<AppearancePanel
					setAttributes = { setAttributes }
					appearance    = { appearance }
					initialOpen   = { false }
				/>
				<FontSizePanel
					setAttributes = { setAttributes }
					titleTag      = { titleTag }
					fontSize      = { fontSize }
					initialOpen   = { false }
				/>
			</InspectorControls>
			<div
				className = 'image-selector'
			>
				{ objectID ?
					<ImageSelector 
						imgHeight     = { imgHeight }
						imgWidth      = { imgWidth }
						objectID      = { objectID }
						imgIndex      = { imgIndex }
						imgURL        = { imgURL }
						imgDimensions = { imgDimensions }
						setAttributes = { setAttributes }
						totalImages   = { totalImages }
						setImageReady = { this.setImageReady }
						imgReady      = { imgReady }
					/>
					:
					<div>
						<div>Click 'Search' to embed object.</div>
						<ObjectSearchButton
							returnCallback = { this.onSearchModalReturn }
						>
							Search
						</ObjectSearchButton>
					</div>
				}
				{ displayTitle && 
					<TitleTag
						className = 'image-selector-title'
					>
							{ title }
					</TitleTag>
				}
				<div
					style={ { fontSize: fontSize + 'em'  } }
				>
					{ displayCatID && 
						<div>{ catID }</div>
					}
					{ objectID && displayCaption &&
						<RichText
							tagName            = 'p'
							className          = 'caption-text-field'
							value              = { captionText } 
							formattingControls = { [ 'bold', 'italic', 'link' ] } 
							onChange           = { ( content ) => setAttributes( { captionText : content } ) } 
							placeholder        = { __( 'Enter caption...' ) } 
						/>
					}
				</div>
			</div>
			</>
		);
	}
}

export default ObjectImageEdit;