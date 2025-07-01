import { useState, useCallback, useEffect } from "@wordpress/element";

export const useKindForm = (initialKindData, onSave) => {
  const [formData, setFormData] = useState(initialKindData || {});
  const [isDirty, setIsDirty] = useState(false);
  const [isSaving, setIsSaving] = useState(false);
  const [lastSaveTime, setLastSaveTime] = useState(null);
  const [saveError, setSaveError] = useState(null);

  // Update form data when initial data changes, but only if user isn't actively editing
  useEffect(() => {
    if (initialKindData && !isDirty) {
      setFormData(initialKindData);
      setIsDirty(false);
    }
  }, [initialKindData, isDirty]);

  const updateField = useCallback((fieldName, value) => {
    setFormData((prev) => {
      // Only update if the value actually changed
      if (prev[fieldName] === value) {
        return prev;
      }

      return {
        ...prev,
        [fieldName]: value,
      };
    });
    setIsDirty(true);
  }, []);

  const handleInputChange = useCallback(
    (fieldName) => (event) => {
      const value =
        event.target.type === "checkbox"
          ? event.target.checked
          : event.target.value;

      // Convert empty strings to null for consistency with existing behavior
      const processedValue = value === "" ? null : value;
      updateField(fieldName, processedValue);
    },
    [updateField],
  );

  const save = useCallback(async () => {
    if (!isDirty || isSaving) return;

    setIsSaving(true);
    setSaveError(null);

    try {
      await onSave(formData);
      setIsDirty(false);
      setLastSaveTime(new Date());
    } catch (error) {
      const errorMessage = error.message || "Save failed";
      setSaveError(errorMessage);
      console.error("Kind form save failed:", error);
      throw error;
    } finally {
      setIsSaving(false);
    }
  }, [formData, isDirty, isSaving, onSave]);

  const reset = useCallback(() => {
    setFormData(initialKindData || {});
    setIsDirty(false);
    setSaveError(null);
  }, [initialKindData]);

  const clearError = useCallback(() => {
    setSaveError(null);
  }, []);

  return {
    formData,
    isDirty,
    isSaving,
    lastSaveTime,
    saveError,
    updateField,
    handleInputChange,
    save,
    reset,
    clearError,
  };
};
