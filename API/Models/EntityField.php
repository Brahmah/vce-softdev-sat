<?php

enum extraValidationTypes: string
{
    case IP = "IP";
    case MAC = "MAC";
    case AREA = "AREA";
}

enum fieldSectionType: string
{
    case overview = "Overview";
    case specs = "Specifications";
    case connectivity = "Connectivity";
    case hidden = "Hidden";
}

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
    public string $label;
    public string|int|null $value;
    public string $placeholder;
    public fieldSectionType $section;
    public string $type;
    public int|null $maxLength;
    public array $extraValidations;
    public string $htmlId;
    public string $eventListen;
    public string $parentEntityId;

    public function __construct(string $label, string|int|null $value, string $placeholder, fieldSectionType $section, string $type, int|null $maxLength = null, array $extraValidations = [])
    {
        $this->label = $label;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->section = $section;
        $this->type = $type;
        $this->maxLength = $maxLength;
        $this->extraValidations = $extraValidations;
        $this->parentEntityId = '';
        // generate html id as section and label with no spaces
        $this->htmlId = str_replace(" ", "", $this->section->value) . "-" . str_replace(" ", "", $label);
        $this->htmlId = hash("md5", $this->htmlId);
        // appropriate event listener
        $this->eventListen = match ($type) {
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

    // Validations
    public function validate(string $newValue): EntityFieldValidationResult
    {
        // validate max length
        if (strlen($newValue) > $this->maxLength) {
            return new EntityFieldValidationResult(false, 'Max length is ' . $this->maxLength, "red");
        } // validate number
        else if ($this->type == 'number' && !is_numeric($_GET['value'])) {
            return new EntityFieldValidationResult(false, 'Must be a number', "red");
        } // validate text
        else if ($this->type == 'text' && !is_string($_GET['value'])) {
            return new EntityFieldValidationResult(false, 'Must be text', "red");
        } // validate checkbox
        else if ($this->type == 'checkbox' && !is_bool($_GET['value'])) {
            return new EntityFieldValidationResult(false, 'Must be a boolean', "red");
        } // success
        else {
            return new EntityFieldValidationResult(true, 'Saved ✅', "green");
        }
    }

}