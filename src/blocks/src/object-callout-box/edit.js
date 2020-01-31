const { Component } = wp.element;

import ObjectPlaceholder from "../components/object-embed-placeholder.js";

export function getObjectEmbedEditComponent( ) {
	return class extends Component {
		constructor( props ) {
            super( ...arguments );
            this.props = props;

		}
		
		setUrl (event) {
            if ( event ) {
                event.preventDefault();
            }
            const { url } = this.state;
            const { setAttributes } = this.props;
            this.setState( { editingURL: false } );
            setAttributes( { url } );
		}
		
		render() {
			return [ 
				<div>
					<ObjectPlaceholder 
						onSubmit={ setUrl }
	
						/>
				</div>
			];
		}
	}
}