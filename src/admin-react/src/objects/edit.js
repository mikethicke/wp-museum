import { useState, useEffect, useCallback, useRef } from "@wordpress/element";
import { Button, Spinner, Card, CardBody } from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";

import FieldEdit from "./field-edit";
import KindSettings from "./kind-settings";
import { navigateToMain } from "../router";
import Breadcrumbs from "../components/breadcrumbs";

const Edit = (props) => {
  const { kindItem, kinds, updateKind, saveKindData, isSaving, setIsSaving } =
    props;

  const {
    kind_id: kindId,
    label: kindLabel,
    type_name: kindPostType,
  } = kindItem;

  const baseRestPath = "/wp-museum/v1";

  const dimensionsDefault = {
    n: 1,
    labels: ["", "", ""],
  };

  const [fieldData, setFieldData] = useState(null);
  const [nextFieldId, setNextFieldId] = useState(-1);
  const [hasUnsavedChanges, setHasUnsavedChanges] = useState(false);
  const [hasUnsavedKindChanges, setHasUnsavedKindChanges] = useState(false);
  const [lastSaveTime, setLastSaveTime] = useState(null);
  const [saveError, setSaveError] = useState(null);

  // Handle field blur events - save field data when focus leaves input
  const handleFieldBlur = useCallback(async () => {
    if (!hasUnsavedChanges || !fieldData) return;

    try {
      await apiFetch({
        path: `${baseRestPath}/${kindPostType}/fields`,
        method: "POST",
        data: fieldData,
      });
      setHasUnsavedChanges(false);
      setLastSaveTime(new Date());
      setSaveError(null);
    } catch (error) {
      console.error("Field save failed:", error);
      setSaveError("Field save failed. Please try again.");
    }
  }, [hasUnsavedChanges, fieldData, kindPostType]);

  // Handle kind blur events - save kind data when focus leaves input
  const handleKindBlur = useCallback(async () => {
    if (!hasUnsavedKindChanges) return;

    try {
      await saveKindData();
      setHasUnsavedKindChanges(false);
      setLastSaveTime(new Date());
      setSaveError(null);
    } catch (error) {
      console.error("Kind save failed:", error);
      setSaveError("Kind save failed. Please try again.");
    }
  }, [hasUnsavedKindChanges, saveKindData]);

  const refreshFieldData = useCallback(() => {
    if (!kindPostType || kindPostType === "null") {
      return;
    }

    apiFetch({ path: `${baseRestPath}/${kindPostType}/fields` })
      .then((data) => {
        setFieldData(data);
        setHasUnsavedChanges(false);
      })
      .catch((error) => {
        console.error("Failed to load field data:", error);
        setSaveError("Failed to load field data.");
      });
  }, [kindPostType]);

  useEffect(() => {
    if (!fieldData) {
      refreshFieldData();
    }
  }, [fieldData, refreshFieldData]);

  const updateKindData = (field, event) => {
    updateKind(kindId, field, event);
    setHasUnsavedKindChanges(true);
  };

  const doManualSave = async () => {
    if (isSaving) return;

    setIsSaving(true);
    setSaveError(null);

    try {
      const promises = [];

      if (hasUnsavedChanges && fieldData) {
        promises.push(
          apiFetch({
            path: `${baseRestPath}/${kindPostType}/fields`,
            method: "POST",
            data: fieldData,
          }),
        );
      }

      if (hasUnsavedKindChanges) {
        promises.push(saveKindData());
      }

      await Promise.all(promises);

      setHasUnsavedChanges(false);
      setHasUnsavedKindChanges(false);
      setLastSaveTime(new Date());
    } catch (error) {
      console.error("Manual save failed:", error);
      setSaveError("Save failed. Please try again.");
    } finally {
      setIsSaving(false);
    }
  };

  const updateField = (fieldId, fieldItem, changeEvent) => {
    const newValue =
      changeEvent.target.type === "checkbox"
        ? changeEvent.target.checked
        : changeEvent.target.value;

    // Handle dimensions separately
    if (fieldItem.startsWith("dimension")) {
      const newFieldData = Object.assign({}, fieldData);
      const [dimension, key, index] = fieldItem.split(".");
      const dimensionsField = fieldData[fieldId]["dimensions"];
      const newDimensionData = dimensionsField
        ? dimensionsField
        : dimensionsDefault;
      if (key == "n") {
        newDimensionData.n = newValue;
      } else {
        newDimensionData[key][index] = newValue;
      }
      newFieldData[fieldId]["dimensions"] = newDimensionData;
      setFieldData(newFieldData);
      setHasUnsavedChanges(true);
      return;
    }

    // Update field data immediately
    const newFieldData = Object.assign({}, fieldData);
    if (fieldData[fieldId][fieldItem] !== newValue) {
      newFieldData[fieldId][fieldItem] = newValue;
      setFieldData(newFieldData);
      setHasUnsavedChanges(true);
    }
  };

  const deleteField = async (fieldId) => {
    const newFieldData = Object.assign({}, fieldData);
    newFieldData[fieldId]["delete"] = true;
    setFieldData(newFieldData);

    try {
      await apiFetch({
        path: `${baseRestPath}/${kindPostType}/fields`,
        method: "POST",
        data: newFieldData,
      });
      refreshFieldData();
    } catch (error) {
      console.error("Failed to delete field:", error);
      setSaveError("Failed to delete field.");
    }
  };

  const updateFactors = (fieldId, newFactors) => {
    if (
      JSON.stringify(fieldData[fieldId]["factors"]) !==
      JSON.stringify(newFactors)
    ) {
      const newFieldData = Object.assign({}, fieldData);
      newFieldData[fieldId]["factors"] = newFactors;
      setFieldData(newFieldData);
      setHasUnsavedChanges(true);
    }
  };

  const moveItem = (fieldId, move) => {
    const oldOrder = fieldData[fieldId]["display_order"];
    const targetOrder = oldOrder + move;
    if (targetOrder < 0) return;

    const fieldValues = Object.values(fieldData);
    if (targetOrder >= fieldValues.length) return;

    const targetIndex = fieldValues.findIndex(
      (fieldItem) => fieldItem["display_order"] == targetOrder,
    );
    const targetKey = fieldValues[targetIndex]["field_id"];

    const newFieldData = Object.assign({}, fieldData);
    newFieldData[fieldId]["display_order"] = targetOrder;
    newFieldData[targetKey]["display_order"] = oldOrder;
    setFieldData(newFieldData);
    setHasUnsavedChanges(true);
  };

  const defaultFieldData = {
    field_id: 0,
    slug: "",
    kind_id: kindId,
    name: "",
    type: "plain",
    display_order: 0,
    public: true,
    required: false,
    quick_browse: false,
    help_text: "",
    detailed_instructions: "",
    public_description: "",
    field_schema: "",
    max_length: 0,
    dimensions: dimensionsDefault,
    factors: [],
    units: "",
  };

  const addField = async () => {
    const updatedFieldData = fieldData ? Object.assign({}, fieldData) : {};
    updatedFieldData[nextFieldId] = { ...defaultFieldData };
    updatedFieldData[nextFieldId]["field_id"] = nextFieldId;

    if (fieldData && Object.values(fieldData).length > 0) {
      const sortedFields = Object.values(fieldData).sort((a, b) =>
        a["display_order"] < b["display_order"] ? 1 : -1,
      );
      updatedFieldData[nextFieldId]["display_order"] =
        sortedFields[0]["display_order"] + 1;
    }

    setNextFieldId(nextFieldId - 1);
    setFieldData(updatedFieldData);
    setHasUnsavedChanges(true);

    try {
      await apiFetch({
        path: `${baseRestPath}/${kindPostType}/fields`,
        method: "POST",
        data: updatedFieldData,
      });
      refreshFieldData();
    } catch (error) {
      console.error("Failed to add field:", error);
      setSaveError("Failed to add field.");
    }
  };

  const handleBackClick = () => {
    const hasChanges = hasUnsavedChanges || hasUnsavedKindChanges;
    if (hasChanges) {
      if (
        confirm("You have unsaved changes. Are you sure you want to leave?")
      ) {
        navigateToMain();
      }
    } else {
      navigateToMain();
    }
  };

  useEffect(() => {
    const handleBeforeUnload = (e) => {
      const hasChanges = hasUnsavedChanges || hasUnsavedKindChanges;
      if (hasChanges) {
        e.preventDefault();
        e.returnValue =
          "You have unsaved changes. Are you sure you want to leave?";
        return "You have unsaved changes. Are you sure you want to leave?";
      }
    };

    window.addEventListener("beforeunload", handleBeforeUnload);
    return () => window.removeEventListener("beforeunload", handleBeforeUnload);
  }, [hasUnsavedChanges, hasUnsavedKindChanges]);

  const getSaveStatusText = () => {
    if (isSaving) return "Saving...";
    if (hasUnsavedChanges || hasUnsavedKindChanges) return "Unsaved changes";
    if (lastSaveTime) return `Last saved: ${lastSaveTime.toLocaleTimeString()}`;
    return "";
  };

  const getSaveStatusClass = () => {
    if (isSaving) return "is-saving";
    if (hasUnsavedChanges || hasUnsavedKindChanges) return "unsaved-warning";
    if (lastSaveTime) return "saved-indicator";
    return "";
  };

  let fieldForms;
  if (fieldData) {
    fieldForms = Object.values(fieldData)
      .filter(
        (dataItem) => typeof dataItem.delete == "undefined" || !dataItem.delete,
      )
      .sort((a, b) => (a["display_order"] > b["display_order"] ? 1 : -1))
      .map((dataItem) => (
        <Card key={dataItem["field_id"]} className="field-card">
          <CardBody>
            <FieldEdit
              fieldData={dataItem}
              updateField={updateField}
              updateFactors={updateFactors}
              deleteField={deleteField}
              moveItem={moveItem}
              dimensionsDefault={dimensionsDefault}
              onFieldBlur={handleFieldBlur}
            />
          </CardBody>
        </Card>
      ));
  }

  return (
    <div className="edit-container">
      <Breadcrumbs />

      <div className="edit-header">
        <Button onClick={handleBackClick} variant="secondary">
          ← Back to Objects
        </Button>

        <div className="header-title">
          <h1>
            {kindLabel}
            {(hasUnsavedChanges || hasUnsavedKindChanges) && (
              <span className="unsaved-indicator">*</span>
            )}
          </h1>
        </div>

        <div className="header-actions">
          <div className={`save-status ${getSaveStatusClass()}`}>
            {isSaving && <Spinner />}
            <span>{getSaveStatusText()}</span>
          </div>

          {saveError && <div className="save-error">{saveError}</div>}

          <Button
            onClick={doManualSave}
            variant="primary"
            isBusy={isSaving}
            disabled={
              isSaving || (!hasUnsavedChanges && !hasUnsavedKindChanges)
            }
          >
            Save Changes
          </Button>
        </div>
      </div>

      <div className="edit-content">
        <div className="main-panel">
          <Card className="kind-settings-card">
            <CardBody>
              <h2>Object Settings</h2>
              <KindSettings
                kindData={kindItem}
                fieldData={fieldData}
                kinds={kinds}
                updateKindData={updateKindData}
                onBlur={handleKindBlur}
              />
            </CardBody>
          </Card>

          <div className="fields-section">
            <div className="fields-header">
              <h2>Fields</h2>
              <Button
                onClick={addField}
                variant="secondary"
                disabled={isSaving}
              >
                Add New Field
              </Button>
            </div>

            <div className="fields-list">
              {fieldForms && fieldForms.length > 0 ? (
                fieldForms
              ) : (
                <Card className="empty-state">
                  <CardBody>
                    <p>
                      No fields configured yet. Add your first field to get
                      started.
                    </p>
                  </CardBody>
                </Card>
              )}
            </div>
          </div>
        </div>

        <div className="help-panel">
          <Card>
            <CardBody>
              <h3>Field Types</h3>
              <ul>
                <li>
                  <strong>Plain Text:</strong> Simple text input
                </li>
                <li>
                  <strong>Rich Text:</strong> WYSIWYG editor
                </li>
                <li>
                  <strong>Date:</strong> Date picker
                </li>
                <li>
                  <strong>Measure:</strong> Numeric values with units
                </li>
                <li>
                  <strong>Factor:</strong> Single selection from predefined
                  options
                </li>
                <li>
                  <strong>Multiple Factor:</strong> Multiple selections
                </li>
                <li>
                  <strong>Flag:</strong> Yes/No checkbox
                </li>
              </ul>
            </CardBody>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default Edit;
