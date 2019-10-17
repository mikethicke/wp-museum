jQuery(document).ready(function($) {
    function backup_images(post_index) {
        var progress_bar = document.getElementById('image-upload-progress');
        progress_bar.innerHTML = post_index + '/' + image_backup_data.total_length;
        if (post_index <= image_backup_data.total_length) {
            var post_id = -1;
            if (post_index < image_backup_data.total_length) {
                post_id = image_backup_data.object_list[post_index];
            }
            var data = {
                'action': 'iterate_image_backup',
                'post_id': post_id,
                'zipfile': image_backup_data.zipfile,
                'zip_mode': image_backup_data.zip_mode
            };
            $.post(ajaxurl, data, function(response) {
                post_index++;
                backup_images(post_index);
            });
        } else {
            //clear percentage bar
            //refresh file list
            var a = 0;
        }
    }
    backup_images(0);
});