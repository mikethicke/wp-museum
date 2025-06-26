import apiFetch from "@wordpress/api-fetch";
import { useState, useEffect, useRef } from "@wordpress/element";
import { Button } from "@wordpress/components";

import Edit from "./edit";

const ObjectAdminControl = () => {
  const [selectedPage, setSelectedPage] = useState("main");
  const [isSaving, setIsSaving] = useState(false);
  const [kindItem, setKindItem] = useState(null);
  const [newKindCount, updateNewKindCount] = useState(1);
  const savingNewItemRef = useRef(null);

  const baseRestPath = "/wp-museum/v1";
  const [objectKinds, updateObjectKinds] = useState(null);
  const [kindIds, setKindIds] = useState(null);

  useEffect(() => {
    if (!objectKinds) {
      refreshKindData();
    }
  });

  useEffect(() => maybeSaveKindData(), [objectKinds]);

  const refreshKindData = () => {
    apiFetch({ path: `${baseRestPath}/mobject_kinds` }).then((result) => {
      if (
        !objectKinds ||
        JSON.stringify(result) != JSON.stringify(objectKinds)
      ) {
        setObjectKinds(result);
      }
    });
  };

  const setObjectKinds = (newKindArray) => {
    console.log("setObjectKinds called", {
      currentKindItem: kindItem,
      savingNewItem: savingNewItemRef.current,
      newArrayLength: newKindArray?.length,
      newArrayIds: newKindArray?.map((item) => item.kind_id),
    });

    updateObjectKinds(newKindArray);
    if (!kindItem || !newKindArray) return;

    let kindItemIndex = newKindArray.findIndex(
      (item) => item.kind_id == kindItem.kind_id,
    );

    console.log("Initial find result", {
      kindItemIndex,
      lookingForId: kindItem.kind_id,
    });

    // If not found and we were saving a new item, find it by matching properties
    if (kindItemIndex === -1 && savingNewItemRef.current) {
      console.log("Trying to match saved item", savingNewItemRef.current);
      kindItemIndex = newKindArray.findIndex(
        (item) =>
          item.label === savingNewItemRef.current.label && item.kind_id > 0,
      );
      console.log("Match result", {
        kindItemIndex,
        foundItem: newKindArray[kindItemIndex],
      });
      // Clear the saving state once we've found the item
      if (kindItemIndex !== -1) {
        savingNewItemRef.current = null;
      }
    }

    if (kindItemIndex === -1) {
      console.log("Setting kindItem to null - item not found");
      setKindItem(null);
    } else {
      console.log(
        "Setting kindItem to found item",
        newKindArray[kindItemIndex],
      );
      setKindItem(newKindArray[kindItemIndex]);
    }
  };

  const updateKind = (kindId, field, event) => {
    const kindIndex = objectKinds.findIndex(
      (kindItem) => kindItem.kind_id == kindId,
    );
    if (kindIndex === -1) return;

    const newKindArray = objectKinds.concat([]);
    if (event.target.type === "checkbox") {
      if (objectKinds[kindIndex][field] != event.target.checked) {
        newKindArray[kindIndex][field] = event.target.checked;
        setObjectKinds(newKindArray);
      }
      return;
    }

    if (objectKinds[kindIndex][field] != event.target.value) {
      newKindArray[kindIndex][field] = event.target.value;
      if (newKindArray[kindIndex][field] == "") {
        newKindArray[kindIndex][field] = null;
      }
      setObjectKinds(newKindArray);
    }
  };

  const defaultKind = {
    kind_id: 0 - newKindCount,
    cat_field_id: null,
    name: null,
    type_name: "new_object_type",
    label: "New Object Type",
    label_plural: null,
    description: null,
    categorized: false,
    hierarchical: false,
    must_featured_image: false,
    must_gallery: false,
    strict_checking: false,
    exclude_from_search: false,
    parent_kind_id: null,
  };

  const newKind = () => {
    const newKind = Object.assign({}, defaultKind);
    newKind.kind_id = 0 - newKindCount;
    updateNewKindCount(newKindCount + 1);
    const newObjectKinds = objectKinds.concat([newKind]);
    updateObjectKinds(newObjectKinds);
    setKindItem(newKind);
    setSelectedPage("edit");
  };

  const saveKindData = () => {
    console.log("saveKindData called", {
      currentKindItem: kindItem,
      isNewItem: kindItem?.kind_id < 0,
    });
    setIsSaving(true);
    // Track if we're saving a new item
    if (kindItem && kindItem.kind_id < 0) {
      console.log("Setting savingNewItem", kindItem);
      savingNewItemRef.current = kindItem;
    }
    apiFetch({
      path: `${baseRestPath}/mobject_kinds`,
      method: "POST",
      data: objectKinds,
    }).then(() => {
      console.log("Save complete, calling refreshKindData");
      refreshKindData();
      setIsSaving(false);
    });
  };

  const maybeSaveKindData = () => {
    const currentIds = objectKinds
      ? JSON.stringify(objectKinds.map((kindItem) => kindItem.kind_id))
      : null;
    if (!kindIds || kindIds != currentIds) {
      setKindIds(currentIds);
      // Don't auto-save if we have new items with negative IDs
      const hasNewItems =
        objectKinds && objectKinds.some((item) => item.kind_id < 0);
      if (!hasNewItems) {
        saveKindData();
      }
    }
  };

  const deleteKind = (kindItem) => {
    let confirmDelete = confirm(
      "Really delete kind? Objects associated with this kind will remain in database but will be inaccessible.",
    );
    if (confirmDelete) {
      kindItem.delete = true;
      saveKindData();
    }
  };

  const editKind = (newKindItem) => {
    setKindItem(newKindItem);
    setSelectedPage("edit");
  };

  const exportKind = async (kindItem) => {
    try {
      // Fetch fields data first
      const fieldsData = await apiFetch({
        path: `${baseRestPath}/${kindItem.type_name}/fields`,
      });

      // Create a new object with fields data included
      const kindItemWithFields = {
        ...kindItem,
        fields: fieldsData,
      };

      // Export the enhanced object
      const kindItemJSON = JSON.stringify(kindItemWithFields, null, 2);
      const blob = new Blob([kindItemJSON], { type: "application/json" });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      const filename = `${kindItem.label || "object-kind"}.json`;
      a.href = url;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      setTimeout(() => {
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
      }, 100);
    } catch (error) {
      console.error("Error exporting kind:", error);
      alert("Failed to export kind. See console for details.");
    }
  };

  switch (selectedPage) {
    case "main":
      return (
        <Main
          objectKinds={objectKinds}
          editKind={editKind}
          newKind={newKind}
          deleteKind={deleteKind}
          exportKind={exportKind}
        />
      );
    case "edit":
      if (kindItem) {
        return (
          <Edit
            kinds={objectKinds}
            kindItem={kindItem}
            updateKind={updateKind}
            saveKindData={saveKindData}
            isSaving={isSaving}
            setIsSaving={setIsSaving}
            setSelectedPage={setSelectedPage}
          />
        );
      } else {
        return null;
      }
  }
};

const Main = (props) => {
  const { objectKinds, editKind, newKind, deleteKind, exportKind } = props;

  if (objectKinds) {
    const kindRows = objectKinds
      .filter(
        (kindItem) =>
          typeof kindItem.delete === "undefined" || !kindItem.delete,
      )
      .map((kindItem, index) => (
        <div key={index}>
          <div>{kindItem.label}</div>
          <div className="object-action-buttons">
            <Button onClick={() => editKind(kindItem)} isLarge isSecondary>
              Edit
            </Button>
            <Button isLarge isSecondary onClick={() => deleteKind(kindItem)}>
              Delete
            </Button>
            <Button isLarge isSecondary>
              Export CSV
            </Button>
            <Button isLarge isSecondary>
              Import CSV
            </Button>
            <Button isLarge isSecondary onClick={() => exportKind(kindItem)}>
              Export Kind
            </Button>
          </div>
        </div>
      ));

    return (
      <div className="museum-admin-main">
        <h1>Museum Administration</h1>
        <div>{kindRows}</div>
        <div>
          <Button onClick={newKind} isLarge isSecondary>
            Add New
          </Button>
        </div>
      </div>
    );
  } else {
    return <div></div>;
  }
};

export { ObjectAdminControl as ObjectPage };
