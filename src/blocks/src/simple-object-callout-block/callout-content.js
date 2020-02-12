import {
	Component
} from '@wordpress/element';

class CalloutContent extends Component {

	render()  {
		const { object_id, title, excerpt, thumbnail, object_link, fields } = this.props;

		const body = [
			  <>
				{ thumbnail === null || 
					<img src = { thumbnail } />
				}
				{ title === null || 
				<h2>{ title }</h2>
				}
				{ excerpt === null ||
				<p>{ excerpt } </p>
				}
			  </>
		];
		
		let linked_body;
		if ( object_link !== null ) {
			linked_body = [
				<a href = { object_link }>{ body }</a>
			];
		} else {
			linked_body = body;
		}

		if ( object_id !== null ) {	
			return [
				<div>
					{ linked_body }
				</div>
			]
		} else {
			return [
				<div>

				</div>
			];
		}
	}
}

export default CalloutContent;