/**
 * Inspector panel to set font size and title style.
 */

/**
 * WordPress dependencies
 */
import { 
	PanelBody,
	PanelRow,
	SelectControl,
	RangeControl,
} from '@wordpress/components';

import { __ } from "@wordpress/i18n";

/**
 * Creates an inspector panel (must be inside <InspectorControls>) for selecting
 * font size and title tag for the block.
 * 
 * @param {object}   props               The component properties.
 * @param {function} props.setAttributes Callback to set attributes for block.
 * @param {string}   props.titleTag      The tag for the block title (h2-h6 or p).
 * @param {number}   props.fontSize      Font size for normal text in the block, in em.
 * @param {boolean}  props.initialOpen   Whether the panel should be open initially. 
 */
const FontSizePanel = ( props ) => {
	const { setAttributes, titleTag, fontSize, initialOpen } = props;

	const titleTagOptions = [
		{ label: 'Heading 2', value: 'h2' },
		{ label: 'Heading 3', value: 'h3' },
		{ label: 'Heading 4', value: 'h4' },
		{ label: 'Heading 5', value: 'h5' },
		{ label: 'Heading 6', value: 'h6' },
		{ label: 'Paragraph', value: 'p' },
	];

	return (
		<PanelBody
			title       = { __( 'Font Size' ) }
			initialOpen = { initialOpen }
		>
			<PanelRow>
				<SelectControl
					label    = { __( 'Title Style' ) }
					value    = { titleTag }
					options  = { titleTagOptions }
					onChange = { ( val ) => setAttributes( { titleTag: val } ) }
				/>
			</PanelRow>
			<PanelRow>
				<RangeControl
					label           = { __( 'Text (em)' ) }
					onChange        = { ( val ) => val ? setAttributes( { fontSize: val } ) : setAttributes( { fontSize: 1 } ) }
					min             = '0.25'
					max             = '2'
					step            = '0.05'
					value           = { fontSize }
					initialPosition = '1'
					withInputField
					allowReset
				/>
			</PanelRow>
		</PanelBody>
	);
}

export default FontSizePanel;