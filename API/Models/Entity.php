<?php
require_once 'EntityField.php';
require_once 'Area.php';

class Entity
{
    // Properties
    public int $id;
    public string $type_id;
    public string|null $type_label;
    public string|null $area_id;
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
    public array $fields;
    public Area|null $area;

    // Meta
    public bool $isExpandedSections;

    // Constructor
    public function __construct(array $raw_db_entity, mixed $customFields = null)
    {
        $this->isExpandedSections = false;
        // Set Properties, Not doing so dynamically to maintain autocomplete
        $this->id = $raw_db_entity['id'];
        $this->type_id = $raw_db_entity['type_id'];
        if (isset($raw_db_entity['type_label'])) {
            $this->type_label = $raw_db_entity['type_label'];
        }
        $this->area_id = $raw_db_entity['area_id'];
        $this->status = $raw_db_entity['status'];
        $this->name = $raw_db_entity['name'];
        $this->ip_address = $raw_db_entity['ip_address'];
        $this->mac_address = $raw_db_entity['mac_address'];
        $this->serial_number = $raw_db_entity['serial_number'];
        $this->manufacturer = $raw_db_entity['manufacturer'];
        $this->model = $raw_db_entity['model'];
        $this->connection_type = $raw_db_entity['connection_type'];
        $this->brief_notes = $raw_db_entity['brief_notes'];
        $this->created_date = str_replace(' ', 'T', $raw_db_entity['created_date']);
        // Populate Fields
        $this->populateBaseFields();
        if ($customFields !== null) {
            $this->populateCustomFields($customFields);
        }
        // Populate Sections
        $this->populateSections();
        // Populate Area if exist
        if ($raw_db_entity['area_id'] != null && isset($raw_db_entity['area_name'])) {
            $this->area = new Area($raw_db_entity, true); // Inner joined so will work
        }
    }

    // Methods
    /**
     * Populates default base fields that exist for every entity.
     * @return void
     */
    private function populateBaseFields()
    {
        $this->fields = [
            new EntityField("type_id", true,"Device Type", $this->type_id, "WAP", "Overview", "device_type", 250, []),
            new EntityField("area_id",true,"Area", $this->area_id, "1", "Overview", "area", 250, []),
            new EntityField("status",true,"Status", $this->status, "Active", "Overview", "text", 250, []),
            new EntityField("name",true,"Name", $this->name, "Test Device", "Overview", "text", 250, []),
            new EntityField("ip_address",true,"IP Address", $this->ip_address, "10.132.8.33", "Overview", "text", 250, []),
            new EntityField("mac_address",true,"MAC Address", $this->mac_address, "00:00:00:00:00:00", "Overview", "text", 250, []),
            new EntityField("serial_number", true,"Serial Number", $this->serial_number, "123456789", "Overview", "text", 250, []),
            new EntityField("manufacturer",true, "Manufacturer", $this->manufacturer, "Test Manufacturer", "Overview", "text", 250, []),
            new EntityField("model",true,"Model", $this->model, "Test Model", "Overview", "text", 250, []),
            new EntityField("connection_type", true,"Connection Type", $this->connection_type, "WAP", "Overview", "text", 250, []),
            new EntityField("brief_notes", true, "Brief Notes", $this->brief_notes, "Test Notes", "Overview", "text", 250, []),
            new EntityField("created_date", false, "Created Date", $this->created_date, "2020-01-01", "Overview", "datetime-local", 250, []),
        ];
        // set parentEntityId for each field
        foreach ($this->fields as $field) {
            $field->parentEntityId = $this->id;
        }
    }

    /**
     * Populates custom user-defined fields that exist for a particular entity type.
     * @return void
     */
    private function populateCustomFields(mixed $customFields)
    {
        foreach ($customFields as $field) {
            $this->fields[] = $field;
        }
    }

    /**
     * Populates all sections and their child fields
     * @return void
     */
    private function populateSections()
    {
        // Get list of all section names
        $sectionNames = array_map(function ($field) {
            return $field->section;
        }, $this->fields);
        // Remove duplicates
        $sectionNames = array_unique($sectionNames);
        // Create sections
        foreach ($sectionNames as $sectionName) {
            $sectionFields = array_values(array_filter($this->fields, function ($field) use ($sectionName) {
                return $field->section == $sectionName;
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