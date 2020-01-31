import { registerBlockType } from "@wordpress/blocks";
import { __ } from "@wordpress/i18n";
import {
    InspectorControls,
    PanelColorSettings,
    RichText
} from "@wordpress/editor";

import ObjectSearchButton from '../components/object-search-box.js';
import { getObjectEmbedEditComponent } from './edit.js';

registerBlockType('wp-museum/object-callout-block', {
    title: __('Object Callout'),
    icon: 'archive',
    category: 'widgets',
    edit: ( props ) => { return( getObjectEmbedEditComponent() ) },
    save: (props)  => {
        return [ <
            RichText.Content
            tagName = "h2"
            value = "test" /
            >
        ];
    }

});