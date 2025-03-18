/**
 * Adds a panel to the Document Settings sidebar for controlling collection
 * options.
 */

import { PluginDocumentSettingPanel } from "@wordpress/edit-post";

import { useSelect, useDispatch } from "@wordpress/data";

import { useState, useEffect } from "@wordpress/element";

import {
  CheckboxControl,
  SelectControl,
  RadioControl,
  FormTokenField,
} from "@wordpress/components";

import { museum } from "../../icons";

const CollectionSettingsPanel = (props) => {
  const WPM_PREFIX = "wpm_";

  const { editPost } = useDispatch("core/editor");

  const {
    postType,
    associatedCategory,
    autoCollection,
    includeChildCategories,
    includeSubCollections,
    singlePage,
    objectTags,
  } = useSelect((select) => {
    const { getCurrentPostType, getEditedPostAttribute } =
      select("core/editor");
    const postMeta = getEditedPostAttribute("meta");
    return {
      postType: getCurrentPostType(),
      associatedCategory: postMeta["associated_category"],
      autoCollection: !!postMeta["auto_collection"],
      includeChildCategories: !!postMeta["include_child_categories"],
      includeSubCollections: !!postMeta["include_sub_collections"],
      singlePage: !!postMeta["single_page"],
      objectTags: postMeta["object_tags"] || [],
    };
  }, []);

  if (postType != WPM_PREFIX + "collection") {
    return null;
  }

  const categories = useSelect((select) =>
    select("core").getEntityRecords("taxonomy", "category", { per_page: -1 })
  );
  const categoryOptions = !!categories
    ? categories.map((catRecord) => ({
        label: catRecord.name,
        value: catRecord.id,
      }))
    : [];

  const updateMeta = (metaSlug, metaValue) => {
    editPost({
      meta: {
        [metaSlug]: metaValue,
      },
    });
  };

  // Initialize tokens from the post meta
  const [tokens, setTokens] = useState(objectTags);

  // Update tokens and post meta when the value changes
  const handleTokenChange = (newTokens) => {
    setTokens(newTokens);
    updateMeta("object_tags", newTokens);
  };

  return (
    <PluginDocumentSettingPanel
      name="wpm-collection-settings-panel"
      title="Collection Settings"
      opened={true}
      icon={museum}
    >
      <CheckboxControl
        label="Automatically Add Objects to Collection"
        checked={autoCollection}
        onChange={(val) => updateMeta("auto_collection", val)}
      />
      {autoCollection && (
        <>
          <SelectControl label="Object Type" />
          <FormTokenField
            label="Object tags to include"
            value={tokens}
            onChange={handleTokenChange}
          />
        </>
      )}
      {!autoCollection && (
        <>
          <CheckboxControl
            label="Include Sub Collections"
            checked={includeSubCollections}
            onChange={(val) => updateMeta("include_sub_collections", val)}
          />
        </>
      )}
      <RadioControl
        label="Collection Display"
        help={
          "Should the collection objects and description be " +
          "displayed as a single page or separately with a toggle?"
        }
        selected={singlePage}
        options={[
          { label: "Single Page", value: true },
          { label: "Toggle", value: false },
        ]}
        onChange={(val) =>
          updateMeta("single_page", val === "true" ? true : false)
        }
      />
    </PluginDocumentSettingPanel>
  );
};

export default CollectionSettingsPanel;
