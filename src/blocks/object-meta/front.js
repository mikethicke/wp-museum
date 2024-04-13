import { ObjectPostImageGallery } from "../../components";

const objectImageGalleryElements = document.getElementsByClassName('wpm-objectposttype-image-gallery');
if ( !! objectImageGalleryElements ) {
	for ( let i = 0; i < objectImageGalleryElements.length; i++ ) {
		const objectImageGalleryElement = objectImageGalleryElements[i];
		const postId = parseInt( objectImageGalleryElement.dataset.postId );
		render (
			<ObjectPostImageGallery
				postId = { postId }
			/>,
			objectImageGalleryElement
		);
	}
}