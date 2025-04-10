import { useEffect, useState } from "@wordpress/element";

import {
  getBestImage,
  getFirstObjectImage,
  MaybeLink,
} from "../../javascript/util";

import { fetchObjectImages } from "../../javascript/util";

const ObjectImageBox = (props) => {
  const { object, onClickCallback, imgStyle, linkToObjects } = props;

  const [imgData, setImgData] = useState(null);

  useEffect(() => {
    fetchObjectImages(object.ID).then((result) => {
      if (result) {
        setImgData(result);
      }
    });
  }, [object]);

  if (imgData === null) {
    return (
      <div className="grid-image-wrapper" style={imgStyle}>
        <div className="placeholder-box"></div>
      </div>
    );
  }

  const imgDimensions = {
    height: 300,
    width: 300,
  };

  const bestImage = getBestImage(getFirstObjectImage(imgData), imgDimensions);

  const imgAttrs = {
    src: bestImage.URL,
    title: imgData.title || "",
    alt: imgData.alt || "",
  };

  return (
    <div className="grid-image-wrapper" style={imgStyle}>
      <MaybeLink href={object.URL} doLink={linkToObjects}>
        <img {...imgAttrs} onClick={() => onClickCallback(object.ID) || null} />
      </MaybeLink>
    </div>
  );
};

const ObjectImageGrid = (props) => {
  const { objects, numObjects, columns, linkToObjects, onClickCallback } =
    props;

  const percentWidth = Math.round((1 / columns) * 100) + "%";
  const imgStyle = {
    flexBasis: percentWidth,
  };

  const imageGrid = objects
    .filter((object) => object.imgURL)
    .slice(0, numObjects)
    .map((object, index) => {
      return (
        <ObjectImageBox
          key={index}
          object={object}
          onClickCallback={onClickCallback}
          imgStyle={imgStyle}
          linkToObjects={linkToObjects}
        />
      );
    });

  return <div className="museum-blocks-image-grid">{imageGrid}</div>;
};

export default ObjectImageGrid;
