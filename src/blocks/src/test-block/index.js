import { InspectorControls } from '@wordpress/blockEditor';
import { CheckboxControl } from '@wordpress/components';
import { registerBlockType } from "@wordpress/blocks";
import { Component } from '@wordpress/element';

const edit = class EditComponent extends Component {

    render() {
        const { attributes, setAttributes } = this.props;
        const { an_array, a_boolean } = attributes; 
        return [
            <>
                <InspectorControls>
                    <CheckboxControl 
                        label = 'Checkbox'
                        checked = { an_array[0] === 1 }
                        onChange = { ( val ) => { 
                            an_array.push( an_array[0] );
                            val ? an_array[0] = 1 : an_array[0] = 0;
                            setAttributes( {
                                an_array: an_array,
                                // a_boolean: ! a_boolean
                            } );
                        } } 
                    />
                </InspectorControls>
                <div>
                    Array: { an_array.toString() }
                </div>
            </>
        ];
    }

}

registerBlockType('test/array-not-update', {
    title: 'Array Attribute Change Issue',
    icon: 'universal-access-alt',
    category: 'layout',
    attributes: {
        an_array: {
            type: 'object',
            default: []
        },
        a_boolean: {
            type: 'boolean',
            default: false
        }
    },
    edit,
    save() { return null; },
});