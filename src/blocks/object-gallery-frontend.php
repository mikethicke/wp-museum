<?php
/**
 * Render Object Gallery block on the frontend.
 *
 * @see blocks/src/object-gallery
 *
 * Attributes
 *  - columns        {number}  Number of columns in the grid.
 *  - objectID       {number}  WordPress post_id of the object.
 *  - objectURL      {string}  The URL of the object (ie. a WordPress frontend page).
 *  - imgData        {array}   Array of URLs of images in gallery.
 *  - imgDimensions  {object}  Dimensions for images in the grid. Because
 *                             images vary in size depending on page width,
 *                             this is just used for determining which image
 *                             file to use.
 *  - captionText    {string}  A caption for the block.
 *  - title          {string}  The object's title (name).
 *  - catID          {string}  The museum catalogue id for the object.
 *  - fontSize       {number}  Font size for caption text (em).
 *  - titleTag       {string}  Tag name for the title to use.
 *  - displayTitle   {boolean} Whether to display the title.
 *  - displayCaption {boolean} Whether to display the caption.
 *  - displayCatID   {boolean} Whether to display the object's catalogue ID.
 *  - linkToObject   {boolean} Whether clicking on each image in the grid
 *                             should link to associated image.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

