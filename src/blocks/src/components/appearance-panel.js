/**
 * Panel to control appearance of embedded museum object.
 */

 /**
  * WordPress dependencies
  */
import {
	PanelBody,
	PanelRow,
	RangeControl,
	ColorPicker
} from '@wordpress/components';

/**
 * An inspector panel that allows the user to control the appearance of an
 * embedded museum object. User can control border color & width and background
 * color & opacity.
 * 
 * @param {object}   props                              The component properties.
 * @param {function} props.setAttributes                Function that updates attributes of the block.
 * @param {object}   props.appearance                   Appearance parameters of the object.
 * @param {number}   props.appearance.borderWidth       Width of the border in pixels.
 * @param {string}   props.appearance.borderColor       Color of the border in hex.
 * @param {string}   props.appearance.backgroundColor   Color of the background in hex.
 * @param {number}   props.appearance.backgroundOpacity Opacity of the background, from 0 to 1.
 */
const AppearancePanel = ( props ) => {
	
	const { appearance, setAttributes, initialOpen } = props;
	const { borderWidth, borderColor, backgroundColor, backgroundOpacity } = appearance;
	
	const setAppearance = ( field, val ) => {
		let newVal;
		val ? newVal = val : newVal = 0;
		const newAppearance = Object.assign( {}, appearance );
		if ( field === 'borderColor' || field === 'backgroundColor' ) {
			newVal = newVal.hex;
		}
		newAppearance[ field ] = newVal;
		setAttributes( { appearance: newAppearance } )
	}

	return (
		<PanelBody
			title = "Appearance"
			initialOpen = { initialOpen }
		>
			<PanelRow>
				<RangeControl
					label = 'Border Width'
					allowReset
					initialPosition = '0'
					onChange = { ( val ) => setAppearance( 'borderWidth', val ) }
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
					onChangeComplete = { ( val ) => setAppearance( 'borderColor', val ) }
					disableAlpha
				/>
			</PanelRow>
			<PanelRow>
				<p>Background Color</p>
				<ColorPicker
					color = { backgroundColor }
					onChangeComplete = { ( val ) => setAppearance( 'backgroundColor', val ) }
					disableAlpha
				/>
			</PanelRow>
			<PanelRow>
				<RangeControl
					label = 'Background Opacity'
					allowReset
					initialPosition = '0'
					onChange = { ( val ) => setAppearance( 'backgroundOpacity', val ) }
					min = '0'
					max = '1'
					step = '0.01'
					value = { backgroundOpacity }
				/>
			</PanelRow>
		</PanelBody>
	);
}

export default AppearancePanel;