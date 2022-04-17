<?php
include_once 'EntityField.php';

class Entity
{
    // Properties
    public string $id;
    public string $type_id;
    public string|null $area_id;
    public string|null $campus;
    public string $status;
    public string|null $name;
    public string|null $ip_address;
    public string|null $mac_address;
    public string|null $serial_number;
    public string|null $manufacturer;
    public string|null $model;
    public string|null $connection_type;
    public string|null $brief_notes;
    public string $created_date;

    // Fields
    public array $sections;
    protected array $fields;

    // Constructor

    public function __construct(array $raw_db_entity)
    {
        // Set Properties, Not doing so dynamically to maintain autocomplete
        $this->id = $raw_db_entity['id'];
        $this->type_id = $raw_db_entity['type_id'];
        $this->area_id = $raw_db_entity['area_id'];
        $this->campus = $raw_db_entity['campus'];
        $this->status = $raw_db_entity['status'];
        $this->name = $raw_db_entity['name'];
        $this->ip_address = $raw_db_entity['ip_address'];
        $this->mac_address = $raw_db_entity['mac_address'];
        $this->serial_number = $raw_db_entity['serial_number'];
        $this->manufacturer = $raw_db_entity['manufacturer'];
        $this->model = $raw_db_entity['model'];
        $this->connection_type = $raw_db_entity['connection_type'];
        $this->brief_notes = $raw_db_entity['brief_notes'];
        $this->created_date = $raw_db_entity['created_date'];
        // Populate Fields
        $this->populateFields();
        // Populate Sections
        $this->populateSections();
    }

    // Methods
    private function populateFields()
    {
        $this->fields = [
            new EntityField("Device Type", $this->type_id, "WAP", fieldSectionType::overview, "text", 250, []),
            new EntityField("Area", $this->area_id, "1", fieldSectionType::overview, "text", 250, []),
            new EntityField("Campus", $this->campus, "DP", fieldSectionType::overview, "text", 250, []),
            new EntityField("Status", $this->status, "Active", fieldSectionType::overview, "text", 250, []),
            new EntityField("Name", $this->name, "Test Device", fieldSectionType::overview, "text", 250, []),
            new EntityField("IP Address", $this->ip_address, "10.132.8.33", fieldSectionType::overview, "text", 250, []),
            new EntityField("MAC Address", $this->mac_address, "00:00:00:00:00:00", fieldSectionType::overview, "text", 250, []),
            new EntityField("Serial Number", $this->serial_number, "123456789", fieldSectionType::overview, "text", 250, []),
            new EntityField("Manufacturer", $this->manufacturer, "Test Manufacturer", fieldSectionType::overview, "text", 250, []),
            new EntityField("Model", $this->model, "Test Model", fieldSectionType::overview, "text", 250, []),
            new EntityField("Connection Type", $this->connection_type, "WAP", fieldSectionType::overview, "text", 250, []),
            new EntityField("Brief Notes", $this->brief_notes, "Test Notes", fieldSectionType::overview, "text", 250, []),
            new EntityField("Created Date", $this->created_date, "2020-01-01", fieldSectionType::overview, "text", 250, []),
        ];
        // set parentEntityId for each field
        foreach ($this->fields as $field) {
            $field->parentEntityId = $this->id;
        }
    }

    private function populateSections()
    {
        // Get list of all section names
        $sectionNames = array_map(function ($field) {
            return $field->section->value;
        }, $this->fields);
        // Remove duplicates
        $sectionNames = array_unique($sectionNames);
        // Create sections
        foreach ($sectionNames as $sectionName) {
            $sectionFields = array_values(array_filter($this->fields, function ($field) use ($sectionName) {
                return $field->section->value == $sectionName;
            }));
            // ignore 'Hidden' section
            if ($sectionName == "Hidden") {
                continue;
            }
            // append to sections
            $this->sections[] = [
                "name" => $sectionName,
                "fields" => array_chunk($sectionFields, 2),
                "htmlId" => md5($sectionName)
            ];
        }
    }

}