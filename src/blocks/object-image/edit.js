/**
 * Gutenberg editor view for Object Image block. Creates <ObjectImage> component.
 */

/**
 * WordPress dependencies
 */
import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor'

import { useState } from '@wordpress/element';

import {
	PanelBody,
	CheckboxControl,
} from '@wordpress/components';

import { __ } from "@wordpress/i18n";
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import {
	ImageSizePanel,
	FontSizePanel,
	ImageSelector,
	ObjectEmbedPanel,
	ObjectSearchBox
} from '../../components';

/**
 * Inspector panel controlling whether to display title, caption for the block
 * and whether clicking on images will link to the associated object.
 * 
 * @param {object}   props                The component's properties.
 * @param {function} props.setAttributes  Callback function to update block attributes.
 * @param {boolean}  props.displayTitle   Whether to display a title for the block.
 * @param {boolean}  props.displayCatID   Whether to display catalogue ID for the block.
 * @param {boolean}  props.displayCaption Whether to display a caption for the block.
 * @param {boolean}  props.linkToObject   Whether images should link to objects.
 * @param {boolean}  props.initialOpen    Whether panel should be open by default.
 */
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
				label    = 'Display Title'
				checked  = { displayTitle }
				onChange = { ( val ) => { setAttributes( { displayTitle: val } ) } }
			/>
			<CheckboxControl
				label    = 'Display Catalog ID'
				checked  = { displayCatID }
				onChange = { ( val ) => { setAttributes( { displayCatID: val } ) } }
			/>
			<CheckboxControl
				label    = 'Display Caption'
				checked  = { displayCaption }
				onChange = { ( val ) => { setAttributes( { displayCaption: val } ) } }
			/>
			<CheckboxControl
				label    = 'Link to Object'
				checked  = { linkToObject }
				onChange = { ( val ) => { setAttributes( { linkToObject: val } ) } }
			/>
		</PanelBody>
	);
}

/**
 * Main editor component for Object Image block.
 *
 * This component allows the user to add an image from a museum object to their
 * post. They can select an image from the object's image gallery, show its
 * title and catalogue ID, and add a caption.
 */
const ObjectImageEdit = (props) => {
	const { attributes, setAttributes } = props;
	const [modalOpen, setModalOpen] = useState(false);
	const blockProps = useBlockProps( { className: 'image-selector' } );

	const {
		title,
		catID,
		objectID,
		objectURL,
		imgHeight,
		imgWidth,
		imgDimensions,
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

	const TitleTag = titleTag;

	/**
	 * Callback function from object search box. Sets the post_id for the object.
	 *
	 * @param {number} returnValue WordPress post_id returned from search modal.
	 */
	const onSearchModalReturn = (returnValue) => {
		const base_rest_path = '/wp-museum/v1/';

		if (returnValue != null) {
			setAttributes({ 
				objectID: returnValue,
				imgURL: null,
				imgHeight: null,
				imgWidth: null,
				imgIndex: 0,
				totalImages: 0,
			});

			const object_path = base_rest_path + 'all/' + returnValue;
			apiFetch({ path: object_path }).then(result => {
				setAttributes({
					title: result['post_title'],
					objectURL: result['link'],
					catID: result[result['cat_field']],
				});
			});
		}
	};

	/**
	 * Update image data attributes from ImageSelector.
	 * 
	 * @param {Object} newImageData Image data returned from ImageSelector component
	 */
	const setImgData = (newImageData) => {
		setAttributes(newImageData);

		if (!newImageData.imgURL && newImageData.imgIndex != attributes.imgIndex) {
			setAttributes({ imgURL: null });
		}
	};

	/**
	 * Handle click of image selector placeholder.
	 *
	 * @param {Object} event The click event.
	 */
	const onPlaceholderClick = (event) => {
		event.stopPropagation();
		setModalOpen(true);
	};

	/**
	 * Handle pressing enter on selector placeholder.
	 *
	 * @param {Object} event The onKeyUp event.
	 */
	const onPlaceholderKeyUp = (event) => {
		if (event.key === 'Enter') {
			onPlaceholderClick(event);
		}
	};

	return (
			<div
				{...blockProps}
			>
			<InspectorControls>
				<ObjectEmbedPanel
					onSearchModalReturn={onSearchModalReturn}
					title={title}
					catID={catID}
					objectID={objectID}
					objectURL={objectURL}
					initialOpen={true}
				/>
				<OptionsPanel
					setAttributes={setAttributes}
					displayTitle={displayTitle}
					displayCatID={displayCatID}
					displayCaption={displayCaption}
					linkToObject={linkToObject}
				/>
				<ImageSizePanel
					setAttributes={setAttributes}
					imgHeight={null}
					imgWidth={null}
					imgDimensions={imgDimensions}
					imgAlignment={null}
					initialOpen={true}
				/>
				<FontSizePanel
					setAttributes={setAttributes}
					titleTag={titleTag}
					fontSize={fontSize}
					initialOpen={false}
				/>
			</InspectorControls>
			<>	
				{objectID ?
					<ImageSelector 
						imgHeight={imgHeight}
						imgWidth={imgWidth}
						objectID={objectID}
						imgIndex={imgIndex}
						imgURL={imgURL}
						imgDimensions={imgDimensions}
						setImgData={setImgData}
						totalImages={totalImages}
					/>
					:
					<>
						<div
							className='image-selector-placeholder'
							style={{ minHeight: imgDimensions.height, minWidth: imgDimensions.width }}
							onClick={onPlaceholderClick}
							onKeyUp={onPlaceholderKeyUp}
							role="button"
							tabIndex={0}
						>
							<div
								className='image-selector-placeholder-plus'
							>
								+
							</div>
						</div>
						{modalOpen &&
							<ObjectSearchBox
								close={() => setModalOpen(false)}
								returnCallback={newObjectID => onSearchModalReturn(newObjectID)}
							/>
						}
					</>
				}
				{displayTitle && 
					<TitleTag
						className='image-selector-title'
					>
						{title}
					</TitleTag>
				}
				<div
					style={{ fontSize: fontSize + 'em' }}
				>
					{displayCatID && 
						<div>{catID}</div>
					}
					{displayCaption &&
						<RichText
							tagName="p"
							className="caption-text-field"
							value={captionText} 
							allowedFormats={['core/bold', 'core/italic', 'core/link']} 
							onChange={(content) => setAttributes({ captionText: content })} 
							placeholder={__('Enter caption...')} 
						/>
					}
				</div>
			</>
		</div>
	);
};

export default ObjectImageEdit;