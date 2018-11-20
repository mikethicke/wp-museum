/**
 * Removes an image attachment from an object.
 * Triggered by user clicking "x" on an image.
 *
 * @see remove_image_attachment_aj()
 */
function remove_image_attachment( image_id, post_id ) {
    var data = {
        'action'    : 'remove_image_attachment_aj',
        'post_id'   : post_id,
        'image_id'  : image_id
    };
    
    jQuery.post( ajaxurl, data, function( response ) {
        oib = document.getElementById('object-image-box');
        oib.innerHTML = response;
    });
}

/**
 * Moves an image attachment for object post types when the left or right arrows are clicked.
 *
 * @see swap_image_order_aj()
 */
function wpm_image_move(image_id, direction) {
    div = document.getElementById("image-div-" + image_id);
    gallery_div = document.getElementById("object-image-box");
    gallery_children = gallery_div.children;
    swapped = false;
    
    for ( i = 0; i < gallery_children.length; i++ ) {
        if ( gallery_children[i].id == "image-div-" + image_id ) {
            if ( direction == 1 && i < gallery_children.length - 1 ) {
                swap_div = gallery_children[i + 1];
                gallery_children[i].parentNode.insertBefore( gallery_children[i].parentNode.removeChild(swap_div), gallery_children[i] );
                swapped = true; 
                break;
            }
            else if ( direction == -1 && i > 0 ) {
                swap_div = gallery_children[i - 1];
                gallery_children[i].parentNode.insertBefore( gallery_children[i].parentNode.removeChild(gallery_children[i]), swap_div );
                swapped = true;
                break;
            }
        }
    }
    
    if ( swapped ) {
         var data = {
            'action'    : 'swap_image_order_aj',
            'first_image_id'   : swap_div.id,
            'second_image_id'  : image_id,
            'post_id'          : jQuery("#post_ID").val()
        };
        
        jQuery.post( ajaxurl, data, function( response ) {
            //pass
        });
    }
    
    
}

jQuery(document).ready(function() {
    jQuery("#insert-wpm-image-button").click(function() {
       var frame = wp.media({
            title: 'Select or upload images to Object Gallery',
            button: {
                text: 'Add Image(s)'
            },
            multiple: true
        });

        frame.on( 'select', function() {
            var attachment_ids = document.getElementById('gallery_attach_ids').textContent;
            var selected_items = frame.state().get('selection');
            selected_items.forEach( function (currentValue) {
                if ( attachment_ids != '' ) { attachment_ids += ','; }
                attachment_ids += currentValue.id;
            });
            
            var data = {
                'action'                        : 'add_gallery_images_aj',
                'wpm_gallery_attachment_ids'    : attachment_ids,
                'post_id'                       : jQuery("#post_ID").val()
            };
            jQuery.post( ajaxurl, data, function( response ) {
                jQuery("#object-image-box").html(response);
            });
        });
               
        frame.open();
    });
    
});