import {
	Component
} from '@wordpress/element';

import {
	InspectorControls,
} from '@wordpress/blockEditor'

import { 
	PanelBody,
	RangeControl,
	CheckboxControl,
} from '@wordpress/components';


const GridDimensionsPanel = ( props ) => {
	const {
		initialOpen,
		columns,
		rows,
		setAttributes,
	} = props;
	
	
	return (
		<PanelBody
			initialOpen = { initialOpen }
		>
			<RangeControl
				label    = 'Columns'
				value    = { columns }
				onChange = { ( columns ) => setAttributes( { columns : columns } ) }
				min      = { 2 }
				max      = { 8 }
			/>
			<RangeControl
				label    = 'Rows'
				value    = { rows }
				onChange = { ( rows ) => setAttributes( { rows : rows } ) }
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
		linkToObjects,
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
				checked = { linkToObjects }
				onChange = { ( val ) => setAttributes( { linkToObjects: val } ) }
			/>
		</PanelBody>


	);
}


class ObjectGrid extends Component {


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
			caption,
			displayCaption,
			linkToObjects,
		} = attributes;
		
		
		return (
			<>
				<InspectorControls>
					<GridDimensionsPanel
						initialOpen   = { true }
						columns       = { columns }
						rows          = { rows }
						setAttributes = { setAttributes }
					/>
					<OptionsPanel
						initialOpen    = { true }
						displayTitle   = { displayTitle }
						displayCaption = { displayCaption }
						linkToObjects  = { linkToObjects }
						setAttributes  = { setAttributes }
					/>
				</InspectorControls>
			</>
		);
	}
}


export default ObjectGrid