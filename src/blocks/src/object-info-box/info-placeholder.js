import {
	Component
} from '@wordpress/element';

import {
	TextControl,
	Button,
} from '@wordpress/components';

import ObjectSearchButton from '../components/object-search-box.js';

class InfoPlaceholder extends Component {

	render() {
		const { objectID, onChangeObjectID, onUpdateButton } = this.props;
		return [
			<div>
				<p>Enter the Wordpress ID of the object you wish to display.</p>
				<TextControl
					label = 'Object ID'
					onChange = { onChangeObjectID }
					value = { objectID }
				/>
				<Button isDefault isPrimary
					onClick = { onUpdateButton }
				>
					Update
				</Button>
				<ObjectSearchButton>
					Search
				</ObjectSearchButton>
			</div>

		]
	}
}

export default InfoPlaceholder;