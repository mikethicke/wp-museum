/**
 * Placeholder for embedding museum objects in posts.
 * 
 * @link https://github.com/WordPress/gutenberg/blob/master/packages/block-library/src/embed/embed-placeholder.js
 * @link https://github.com/WordPress/gutenberg/tree/master/packages/components/src/placeholder
 */

 /**
  * Wordpress dependencies.
  */
import { __, _x } from '@wordpress/i18n';
import { Button, Placeholder } from '@wordpress/components';

const ObjectPlaceholder = (props) => {
	return (
		<Placeholder
			icon = 'dashicons-archive'
			label = 'Museum object placeholder'
			classname = 'wpm-object-placeholder'
			instructions = { __('Paste a link or catalog id to the object you wish to embed.') }
		>
			{ onSubmit, onChange, value, label, cannotEmbed } = props;
			<form onSubmit = { onSubmit }>
				<input
					type="text"
					value={ value || '' }
					className="components-placeholder__input"
					aria-label={ label }
					placeholder={ __( 'Enter URL or catalog id to embed…' ) }
					onChange={ onChange } />
				<Button
					isLarge
					type="submit">
					{ _x( 'Embed', 'button label' ) }
				</Button>
				{ cannotEmbed &&
					<p className="components-placeholder__error">
						{ __( 'Sorry, this content could not be embedded.' ) }<br />
						<Button isLarge onClick={ tryAgain }>{ _x( 'Try again', 'button label' ) }</Button> <Button isLarge onClick={ fallback }>{ _x( 'Convert to link', 'button label' ) }</Button>
					</p>
				}
			</form>
		</Placeholder>
	);
};

export default ObjectPlaceholder;