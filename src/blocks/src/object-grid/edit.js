/**
 * Gutenberg editor view for Object Grid block. Creates <ObjectGrid> component.
 */

/**
 * WordPress dependencies
 */
import {
	Component
} from '@wordpress/element';

import {
	InspectorControls,
	RichText
} from '@wordpress/blockEditor'

import { __ } from "@wordpress/i18n";

import { 
	PanelBody,
	RangeControl,
	CheckboxControl,
} from '@wordpress/components';

import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import GridImage from '../components/grid-image';
import FontSizePanel from '../components/font-size-panel';

/**
 * Inspector panel controlling number of rows and columns for the grid.
 * 
 * @param {object}   props               The component's properties.
 * @param {boolean}  props.initialOpen   Whether the panel should be open by default.
 * @param {number}   props.columns       Number of columns for the grid.
 * @param {number}   props.rows          Number of rows for the grid.
 * @param {function} props.updateColumns Callback function to update number of columns.
 * @param {function} props.updateRows    Callback fucntion to update the number of rows.
 */
const GridDimensionsPanel = ( props ) => {
	const {
		initialOpen,
		columns,
		rows,
		updateColumns,
		updateRows
	} = props;

	return (
		<PanelBody
			initialOpen = { initialOpen }
		>
			<RangeControl
				label    = 'Columns'
				value    = { columns }
				onChange = { columns => updateColumns( columns ) }
				min      = { 2 }
				max      = { 8 }
			/>
			<RangeControl
				label    = 'Rows'
				value    = { rows }
				onChange = { rows => updateRows( rows ) }
				min      = { 2 }
				max      = { 12 }
			/>
		</PanelBody>
	);
}

/**
 * Inspector panel controlling whether to display title, caption for the block
 * and whether clicking on images will link to the associated object.
 * 
 * @param {object}   props                The component's properties.
 * @param {boolean}  props.displayTitle   Whether to display a title for the block.
 * @param {boolean}  props.displayCaption Whether to display a caption for the block.
 * @param {boolean}  props.linkToObject   Whether images should link to objects.
 * @param {boolean}  props.initialOpen    Whether panel should be open by default.
 * @param {function} props.setAttributes  Callback function to update block attributes.
 */
const OptionsPanel = ( props ) => {
	const {
		displayTitle,
		displayCaption,
		linkToObject,
		initialOpen,
		setAttributes,
	} = props;

	return (
		<PanelBody
			title       = "Options"
			initialOpen = { initialOpen }
		>
			<CheckboxControl
				label = 'Display Title'
				checked = { displayTitle }
				onChange = { ( val ) => setAttributes( { displayTitle: val } ) }
			/>
			<CheckboxControl
				label = 'Display Caption'
				checked = { displayCaption }
				onChange = { ( val ) => setAttributes( { displayCaption: val } ) }
			/>
			<CheckboxControl
				label = 'Link to Objects'
				checked = { linkToObject }
				onChange = { ( val ) => setAttributes( { linkToObject: val } ) }
			/>
		</PanelBody>


	);
}

/**
 * A Gutenberg block for displaying a grid of museum object images.
 *
 * Images are square and of variable size depending on size of container. User
 * can set number of rows and columns, and the layout will adjust
 * automatically. Internally, the image data are stored in a linear array and
 * referred to by their array index. The images are arranged in rows and
 * columns only on render.
 */
class ObjectGrid extends Component {
	constructor( props ) {
		super( props );
		
		const {
			attributes,
		} = this.props;

		const {
			objectData,
			rows,
			columns
		} = attributes;

		this.updateImgData    = this.updateImgData.bind( this );
		this.updateObjectID   = this.updateObjectID.bind( this );
		this.addNewObjectData = this.addNewObjectData.bind( this );
		this.updateColumns    = this.updateColumns.bind( this );
		this.updateRows       = this.updateRows.bind( this );

		// Creates placeholder data for each cell of the grid.
		this.addNewObjectData( rows * columns - objectData.length );

		this.state = {
			gridImages: null
		}
	}

	/**
	 * Updates the image data for a particular image.
	 * 
	 * @param {number} index            The array index of the image. 
	 * @param {object} imgData          The updated data for that image.
	 * @param {string} imgData.imgURL   New URL for that image.
	 * @param {number} imgData.imgIndex New index for that image (refers to
	 *                                  array index from a particular object's
	 *                                  array of images, not the grid index).
	 */
	updateImgData( index, imgData ) {
		const {
			attributes,
			setAttributes
		} = this.props;

		const {
			objectData
		} = attributes;

		const newObjectData = objectData.concat();

		if ( typeof imgData['imgIndex'] != 'undefined' ) {
			newObjectData[ index ]['imgIndex'] = imgData['imgIndex'];
			newObjectData[ index ]['imgURL'] = null;
		}
		if ( typeof imgData['imgURL'] != 'undefined' ) {
			newObjectData[ index ]['imgURL'] = imgData['imgURL'];
		}

		setAttributes( { objectData: newObjectData } );
	}

	/**
	 * Updates the WordPress post_id for a particular image and then fetches
	 * data for that object from the REST api.
	 *
	 * @param {number}        index       The array index of the image.
	 * @param {number | null} newObjectID New WordPress post_id associated with
	 *                                    that image. If null, reset all of the
	 *                                    data for that index.
	 */
	updateObjectID( index, newObjectID ) {
		const {
			attributes,
			setAttributes
		} = this.props;

		const {
			objectData
		} = attributes;

		if ( objectData[ index ].objectID != newObjectID ) {
			const base_rest_path = '/wp-museum/v1/';
			const newObjectData  = objectData.concat();
			
			// Reset data for the object.
			newObjectData[ index ] = {
				objectID    : newObjectID,
				imgURL      : null,
				imgIndex    : 0,
				objectURL   : null,
				objectTitle : null,
			}

			setAttributes( { objectData: newObjectData } );

			//Fetch new data for the object.
			if ( newObjectID ) {
				const object_path = base_rest_path + 'all/' + newObjectID;
				apiFetch( { path: object_path } ).then( result => {
					const newNewObjectData              = newObjectData.concat();
					newNewObjectData[index].objectURL   = result['link'];
					newNewObjectData[index].objectTitle = result['post_title'];

					setAttributes( { objectData: newNewObjectData } );
				} );
			}
		}
	}

	/**
	 * Adds new placeholder cells to the objectData when the total number of
	 * cells increases.
	 * 
	 * @param {number} numberOfNewCells The number of new cells to add.
	 */
	addNewObjectData ( numberOfNewCells ) {
		if ( numberOfNewCells > 0 ) {
			const {
				objectData,
			} = this.props.attributes;

			const oldLength = objectData.length;

			for ( let gridIndex = oldLength; gridIndex < oldLength + numberOfNewCells; gridIndex++ ) {
				objectData[ gridIndex ] = {
					objectID    : null,
					imgURL      : null,
					imgIndex    : 0,
					objectURL   : null,
					objectTitle : null,
				}
			}
		}
	}

	/**
	 * Callback function to change number of columns in the grid.
	 * 
	 * @param {number} newColumns The new number of columns in the grid.
	 */
	updateColumns( newColumns ) {
		const {
			attributes,
			setAttributes
		} = this.props;

		const {
			rows,
			objectData
		} = attributes;

		// If necessary, add new placeholder data.
		this.addNewObjectData( rows * newColumns - objectData.length );
		
		setAttributes( {
			columns: newColumns
		} );
	}

	/**
	 * Callback function to change number of rows in the grid.
	 * 
	 * @param {number} newRows The number of new rows in the grid.
	 */
	updateRows( newRows ) {
		const {
			attributes,
			setAttributes
		} = this.props;

		const {
			columns,
			objectData
		} = attributes;

		// If necessary, add new placeholder data.
		this.addNewObjectData( newRows * columns - objectData.length );
		
		setAttributes( {
			rows: newRows
		} );
	}

	/**
	 * Render the component.
	 */
	render() {
		const {
			attributes,
			setAttributes
		} = this.props;

		const {
			columns,
			rows,
			title,
			displayTitle,
			captionText,
			displayCaption,
			linkToObject,
			objectData,
			imgDimensions,
			titleTag,
			fontSize
		} = attributes;
		
		const imageGrid = [];
		for ( let row = 0; row < rows; row++ ) {
			let imageRow = [];
			for ( let column = 0; column < columns; column++ ) {
				imageRow.push(
					<GridImage
						key                    = { column + row * columns }
						objectID               = { objectData[ column + row * columns ].objectID }
						imgDimensions          = { imgDimensions }
						imgURL                 = { objectData[ column + row * columns ].imgURL }
						imgIndex               = { objectData[ column + row * columns ].imgIndex }
						updateImgCallback      = { imgData => this.updateImgData( column + row * columns, imgData ) }
						updateObjectIDCallback = { newObjectID => this.updateObjectID( column + row * columns, newObjectID ) }
					/>
				);
			}
			imageGrid.push( imageRow );
		}

		const tableRows = imageGrid.map( ( imageRow, rowIndex ) => (
			<tr
				key = { 'row-' + rowIndex }
			>
				{ imageRow.map( imageItem => (
					<td
						key = { 'cell-' + imageItem.key }
					>{ imageItem }</td>
				) ) }
			</tr>
		) );
	
		const imageTable = (
			<table>
				<tbody>
					{ tableRows }
				</tbody>
			</table>
		);
		
		
		return (
			<>
				<InspectorControls>
					<GridDimensionsPanel
						initialOpen   = { true }
						columns       = { columns }
						rows          = { rows }
						updateColumns = { this.updateColumns }
						updateRows    = { this.updateRows }
					/>
					<OptionsPanel
						initialOpen    = { true }
						displayTitle   = { displayTitle }
						displayCaption = { displayCaption }
						linkToObject   = { linkToObject }
						setAttributes  = { setAttributes }
					/>
					<FontSizePanel
						setAttributes = { setAttributes }
						titleTag      = { titleTag }
						fontSize      = { fontSize }
						initialOpen   = { false }
					/>

				</InspectorControls>
				<div
					className = 'object-grid-container'
				>
					{ displayTitle &&
						<RichText
							tagName            = { titleTag }
							className          = 'title-text-field'
							value              = { title } 
							onChange           = { ( content ) => setAttributes( { title : content } ) } 
							placeholder        = { __( 'Enter title...' ) } 
						/>
					}
					{ imageTable }
					{ displayCaption &&
						<div
							style={ { fontSize: fontSize + 'em'  } }
						>
							<RichText
								tagName            = 'p'
								className          = 'caption-text-field'
								value              = { captionText } 
								formattingControls = { [ 'bold', 'italic', 'link' ] } 
								onChange           = { ( content ) => setAttributes( { captionText : content } ) } 
								placeholder        = { __( 'Enter caption...' ) } 
							/>
						</div>
					}
				</div>
			</>
		);
	}
}


export default ObjectGrid