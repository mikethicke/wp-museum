/**
 * Adds a panel to the Document Settings sidebar for controlling collection
 * options. 
 */

import {
	PluginDocumentSettingPanel
} from '@wordpress/edit-post';

import {
	useSelect,
	useDispatch
} from '@wordpress/data';

import {
	CheckboxControl,
	SelectControl,
	RadioControl
} from '@wordpress/components';

import {
	museum
} from '../../icons';

const CollectionSettingsPanel = props => {
	
	const WPM_PREFIX = 'wpm_';

	const { editPost } = useDispatch( 'core/editor' );
	
	const {
		postType,
		associatedCategory,
		includeChildCategories,
		includeSubCollections,
		singlePage
	} = useSelect (
		( select ) => {
			const {
				getCurrentPostType,
				getEditedPostAttribute
			} = select( 'core/editor' );
			const postMeta = getEditedPostAttribute( 'meta' );
			return {
				postType               : getCurrentPostType(),
				associatedCategory     : postMeta['associated_category'],
				includeChildCategories : !! postMeta['include_child_categories'],
				includeSubCollections  : !! postMeta['include_sub_collections'],
				singlePage             : !! postMeta['single_page'],
			}
		},
		[]
	);
	
	if ( postType != WPM_PREFIX + 'collection' ) {
		return null;
	}

	const categories = useSelect(
		select => select( 'core' ).getEntityRecords( 'taxonomy', 'category', { per_page: -1 } ) 
	);
	const categoryOptions = !! categories ?
		categories.map( catRecord => ( { label: catRecord.name, value: catRecord.id } ) )
		: [];

	const updateMeta = ( metaSlug, metaValue ) => {
		editPost( {
			meta: {
				[ metaSlug ] : metaValue
			}
		} );
	}
	
	return (
		<PluginDocumentSettingPanel
			name   = 'wpm-collection-settings-panel'
			title  = 'Collection Settings'
			opened = { true }
			icon   = { museum }
		>
			<SelectControl
				label    = 'Associated Category'
				value    = { associatedCategory }
				options  = { categoryOptions }
				onChange = { val => updateMeta( 'associated_category', val ) }
			/>
			<CheckboxControl
				label = 'Include Child Categories'
				checked = { includeChildCategories }
				onChange = { val => updateMeta( 'include_child_categories', val ) }
			/>
			<CheckboxControl
				label = 'Include Sub Collections'
				checked = { includeSubCollections }
				onChange = { val => updateMeta( 'include_sub_collections', val ) }
			/>
			<RadioControl
				label = 'Collection Display'
				help = { 'Should the collection objects and description be ' +
						 'displayed as a single page or separately with a toggle?' }
				selected = { singlePage }
				options = { [
					{ label: 'Single Page', value: true },
					{ label: 'Toggle', value: false }
				] }
				onChange = { val => updateMeta( 'single_page', val === 'true' ? true : false ) }
			/>
		</PluginDocumentSettingPanel>
	);
}

export default CollectionSettingsPanel;