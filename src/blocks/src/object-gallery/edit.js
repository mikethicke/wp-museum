/**
 * Gutenberg editor view of for Object Gallery block. Creates <ObjectGallery>
 * component.
 */

/**
 * WordPress dependencies
 */
import {
	Component
} from '@wordpress/element';

import {
	InspectorControls,
} from '@wordpress/blockEditor'

const ColumnsPanel = ( props ) => {
	const {
		initialOpen,
		columns,
		updateColumns
	} = props;

	return (
		<PanelBody
			initialOpen = { initialOpen }
		>
			<RangeControl
				label    = 'Columns'
				value    = { columns }
				onChange = { columns => updateColumns( columns ) }
				min      = { 1 }
				max      = { 8 }
			/>
		</PanelBody>
	);
}


class ObjectGallery extends Component {

	addNewImgData ( numberOfNewCells ) {
		if ( numberOfNewCells > 0 ) {
			const { imgData } = this.props.attributes;
			const oldLength = imgData.length;
			
			for ( let gridIndex = oldLength; gridIndex < oldLength + numberOfNewCells; gridIndex++ ) {
				imgData[ gridIndex ] = {
					imgURL : null,
				}
			}
		}
	}

	render() {
		const {
			attributes
		} = this.props;

		const {
			columns
		} = attributes;


		return (
			<>
				<InspectorControls>
					<ColumnsPanel
						initialOpen = { true }
						columns     = { columns }
						updateColumns = 
					/>

				</InspectorControls>
			</>
		);
	}
}