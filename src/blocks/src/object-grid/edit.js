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

import GridImage from '../components/grid-image';
import FontSizePanel from '../components/font-size-panel';

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

		this.updateImgData = this.updateImgData.bind( this );
		this.updateObjectID = this.updateObjectID.bind( this );
		this.addNewObjectData = this.addNewObjectData.bind( this );
		this.updateColumns = this.updateColumns.bind( this );
		this.updateRows = this.updateRows.bind( this );

		this.addNewObjectData( rows * columns - objectData.length );

		this.state = {
			gridImages: null
		}
	}

	updateImgData( index, imgData ) {
		const {
			attributes,
			setAttributes
		} = this.props;

		const {
			objectData
		} = attributes;

		const newObjectData = objectData.concat();

		if ( imgData['imgURL'] ) {
			newObjectData[index]['imgURL'] = imgData['imgURL'];
		}
		if ( imgData['imgIndex'] ) {
			newObjectData[index]['imgIndex'] = imgData['imgIndex'];
		}

		setAttributes( { objectData: newObjectData } );
	}

	updateObjectID( index, newObjectID ) {
		const {
			attributes,
			setAttributes
		} = this.props;

		const {
			objectData
		} = attributes;

		const base_rest_path = '/wp-museum/v1/';

		const newObjectData = objectData.concat();
		
		newObjectData[index] = {
			objectID: newObjectID,
			imgURL: null,
			imgIndex: 0,
			objectURL: null,
			objectTitle: null,
		}

		setAttributes( { objectData: newObjectData } );

		if ( newObjectID ) {
			const object_path = base_rest_path + 'all/' + newObjectID;
			apiFetch( { path: object_path } ).then( result => {
				const newNewObjectData = newObjectData.concat();
				newNewObjectData[index].objectURL = result['link'];
				newNewObjectData[index].objectTitle = result['post_title'];
				setAttributes( { objectData: newNewObjectData } );
			} );
		}
	}

	addNewObjectData ( numberOfNewCells ) {
		if ( numberOfNewCells > 0 ) {
			const {
				objectData,
			} = this.props.attributes;

			const oldLength = objectData.length;

			for ( let gridIndex = oldLength; gridIndex < oldLength + numberOfNewCells; gridIndex++ ) {
				objectData[ gridIndex ] = {
					objectID: null,
					imgURL: null,
					imgIndex: 0,
					objectURL: null,
					objectTitle: null,
				}
			}
		}
	}

	updateColumns( newColumns ) {
		const {
			attributes,
			setAttributes
		} = this.props;

		const {
			rows,
			objectData
		} = attributes;

		this.addNewObjectData( rows * newColumns - objectData.length );
		
		setAttributes( {
			columns: newColumns
		} );
	}

	updateRows( newRows ) {
		const {
			attributes,
			setAttributes
		} = this.props;

		const {
			columns,
			objectData
		} = attributes;

		this.addNewObjectData( newRows * columns - objectData.length );
		
		setAttributes( {
			rows: newRows
		} );
	}

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
						key = { column + row * columns }
						objectID = { objectData[ column + row * columns ].objectID }
						imgDimensions = { imgDimensions }
						imgURL = { objectData[ column + row * columns ].imgURL }
						imgIndex = { objectData[ column + row * columns ].imgIndex }
						updateImgCallback = { imgData => this.updateImgData( column + row * columns, imgData ) }
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