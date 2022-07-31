<?php

class EntityFieldValidationResult
{
    public bool $valid;
    public string $message;
    public string $color;

    public function __construct(bool $valid, string $message, string $color)
    {
        $this->valid = $valid;
        $this->message = $message;
        $this->color = $color;
    }
}

class EntityField
{
    public string $dbFieldName;
    public string $label;
    public string|int|null $value;
    public string $placeholder;
    public string $section;
    public string $type;
    public int|null $maxLength;
    public array $extraValidations; // Array of additional validations (e.g. ["IP", "MAC", "STORAGE_UNIT"])
    public string $htmlId;
    public string $eventListen;
    public int|null $parentEntityId;
    public bool $isCustom;
    public bool $isEditable;

    public function __construct(string $dbFieldName, bool $isEditable, string $label, string|int|null $value, string $placeholder, string $section, string $type, int|null $maxLength = null, array $extraValidations = [], int|null $parentEntityId = null)
    {
        $this->isCustom = false;
        $this->isEditable = $isEditable;
        $this->dbFieldName = $dbFieldName;
        $this->label = $label;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->section = $section;
        $this->type = $type;
        $this->maxLength = $maxLength;
        $this->extraValidations = $extraValidations;
        $this->parentEntityId = $parentEntityId;
        $this->finaliseConstruct();
    }

    /**
     * Set rest of field parameters.
     * @return void
     */
    public function finaliseConstruct()
    {
        $this->htmlId = $this->dbFieldName;
        if (isset($this->definitionId) && $this->isCustom) {
            $this->htmlId .= $this->definitionId;
        } else {
            // generate html id as section and label with no spaces
            $this->htmlId = str_replace(" ", "", $this->section) . "-" . str_replace(" ", "", $this->label);
        }
        $this->htmlId = hash("md5", $this->htmlId);
        // appropriate event listener
        $this->eventListen = match ($this->type) {
            "text" => "keyup",
            "number" => "keyup",
            "password" => "keyup",
            "checkbox" => "change",
            "select" => "change",
            "date" => "input",
            "datetime-local" => "input",
            default => "change",
        };
    }

    /**
     * @param $newValue
     * @return EntityFieldValidationResult
     */
    public function validate($newValue): EntityFieldValidationResult
    {
        // validate max length
        if (strlen($newValue) > $this->maxLength) {
            return new EntityFieldValidationResult(false, 'Max length is ' . $this->maxLength, "red");
        } // validate number
        else if ($this->type == 'number' && !is_numeric($newValue)) {
            return new EntityFieldValidationResult(false, 'Must be a number', "red");
        } // validate text
        else if ($this->type == 'text' && !is_string($newValue)) {
            return new EntityFieldValidationResult(false, 'Must be text', "red");
        } // validate checkbox
        else if ($this->type == 'checkbox' && !is_bool($newValue)) {
            return new EntityFieldValidationResult(false, 'Must be a boolean', "red");
        } // success
        else {
            return new EntityFieldValidationResult(true, 'Saved ✅', "green");
        }
    }

}

class CustomEntityField extends EntityField
{
    public int $definitionId;

    public function __construct(array $rawDbField, int $parentEntityId)
    {
        $this->isCustom = true;
        $this->isEditable = true;
        $this->dbFieldName = 'custom_' . $rawDbField['definition_id'];
        $this->definitionId = $rawDbField['definition_id'];
        $this->label = $rawDbField['label'];
        $this->value = $rawDbField['field_value'];
        $this->placeholder = $rawDbField['placeholder'];
        $this->section = $rawDbField['section'];
        $this->type = $rawDbField['type'];
        $this->maxLength = $rawDbField['max_length'];
        $this->extraValidations = [];
        $this->parentEntityId = $parentEntityId;
        $this->finaliseConstruct();
    }
}