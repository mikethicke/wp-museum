const KindSettings = (props) => {
  const { kindData, fieldData, kinds, updateKindData, onBlur } = props;

  const {
    kind_id: kindId,
    cat_field_Id: catFieldId,
    description,
    categorized,
    exclude_from_search: excludeFromSearch,
    label,
    label_plural: labelPlural,
    must_featured_image: mustFeaturedImage,
    must_gallery: mustGallery,
    name,
    parent_kind_id: parentKindId,
    strict_checking: strictChecking,
  } = kindData;

  const parentKindOptions = kinds
    .filter((kindItem) => kindItem.kind_id != kindId)
    .map((kindItem) => (
      <option key={kindItem.kind_id} value={kindItem.kind_id}>
        {kindItem.label}
      </option>
    ));

  const idFieldOptions = fieldData
    ? Object.values(fieldData).map((fieldItem) => (
        <option key={fieldItem.field_id} value={fieldItem.field_id}>
          {fieldItem.name}
        </option>
      ))
    : [];

  return (
    <div className="edit-kind-form">
      <label>
        Object Name
        <input
          type="text"
          value={label || ""}
          onChange={(event) => updateKindData("label", event)}
          onBlur={onBlur}
        />
      </label>
      <label>
        Object Name (Plural)
        <input
          type="text"
          value={labelPlural || ""}
          onChange={(event) => updateKindData("label_plural", event)}
          onBlur={onBlur}
        />
      </label>
      <label>
        Description
        <textarea
          value={description || ""}
          onChange={(event) => updateKindData("description", event)}
          onBlur={onBlur}
        />
      </label>
      <label>
        Parent Object
        <select
          value={parentKindId || ""}
          onChange={(event) => updateKindData("parent_kind_id", event)}
        >
          <option></option>
          {parentKindOptions}
        </select>
      </label>
      <label>
        ID Field
        <select
          value={catFieldId || ""}
          onChange={(event) => updateKindData("cat_field_id", event)}
        >
          {idFieldOptions}
        </select>
      </label>
      <div className="kind-options">
        <label>
          <input
            type="checkbox"
            checked={!!strictChecking}
            onChange={(event) => updateKindData("strict_checking", event)}
          />
          Strictly enforce requirements
        </label>
        <label>
          <input
            type="checkbox"
            checked={!!categorized}
            onChange={(event) => updateKindData("categorized", event)}
          />
          Must be categorized
        </label>
        <label>
          <input
            type="checkbox"
            checked={!!mustFeaturedImage}
            onChange={(event) => updateKindData("must_featured_image", event)}
          />
          Must have featured image
        </label>
        <label>
          <input
            type="checkbox"
            checked={!!mustGallery}
            onChange={(event) => updateKindData("must_gallery", event)}
          />
          Must have image gallery
        </label>
        <label>
          <input
            type="checkbox"
            checked={!!excludeFromSearch}
            onChange={(event) => updateKindData("exclude_from_search", event)}
          />
          Exclude from search
        </label>
      </div>
    </div>
  );
};

export default KindSettings;
