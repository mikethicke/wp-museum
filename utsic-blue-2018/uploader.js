jQuery(document).ready(function() {
 
    jQuery('.st_upload_button').click(function() {
         targetfield = jQuery(this).prev('.upload_url');
         tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
         return false;
    });
 
    window.send_to_editor = function(html) {
         imgurl = jQuery('img',html).attr('src');
         if (typeof(imgurl) === 'undefined') {
            turl = jQuery(html).attr('href');
            jQuery(targetfield).val(turl);
         }
         else {
            jQuery(targetfield).val(imgurl);
         }
         tb_remove();
    }
 
});
