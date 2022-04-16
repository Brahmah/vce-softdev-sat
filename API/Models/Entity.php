<?php
include_once 'EntityField.php';

class Entity
{
    // Properties
    public string $id;
    public string $type;
    public string $name;
    public string $campus;
    public string $area;
    public string $ipAddress;
    public string $macAddress;
    public string $location;
    public string $status;
    public string $interfaceUrl;
    public string $adminUsername;
    public string $adminPassword;
    public string $briefNotes;
    public string $purpose;
    public string $manufacturer;
    public string $model;
    public string $serialNumber;
    public string $os;
    public string $osVersion;
    public string $ram_gb;
    public string $connectionType;
    public string $patchBuilding;
    public string $patchPointNumber;
    public string $patchPointSwitchPort;
    public string $vlan_id;
    public string $createdAt;

    // Fields
    protected array $fields;
    public array $sections;

    // Constructor
    public function __construct(string $id)
    {
        // Set Properties
        $this->id = $id;
        $this->type = 'WAP';
        $this->name = 'Front Office WAP';
        $this->campus = 'CC';
        $this->area = '';
        $this->ipAddress = '10.132.8.33';
        $this->macAddress = '00:0c:29:c0:00:00';
        $this->location = 'CC-WAP';
        $this->status = 'Active';
        $this->interfaceUrl = 'https://google.com';
        $this->adminUsername = 'admin';
        $this->adminPassword = 'password';
        $this->briefNotes = 'Front Office WAP';
        $this->purpose = 'Front Office WAP';
        $this->manufacturer = 'Cisco';
        $this->model = 'Cisco WAP';
        $this->serialNumber = '123456789';
        $this->os = 'IOS';
        $this->osVersion = '12.4';
        $this->ram_gb = '4';
        $this->connectionType = 'ethernet';
        $this->patchBuilding = 'CC';
        $this->patchPointNumber = '1';
        $this->patchPointSwitchPort = '1/1';
        $this->vlan_id = '1';
        $this->createdAt = '2020-01-01 00:00:00';
        // Populate Fields
        $this->populateFields();
        // Populate Sections
        $this->populateSections();
    }

    // Methods
    private function populateFields()
    {
        $this->fields = [
            new EntityField("id", $this->id, "0", fieldSectionType::hidden, "text", null, []),
            new EntityField("Device Type", $this->type, "WAP", fieldSectionType::overview, "text", 250, []),
            new EntityField("Name", $this->name, "Test Device", fieldSectionType::overview, "text", 250, []),
            new EntityField("Campus", $this->campus, "Test Campus", fieldSectionType::overview, "text", 250, []),
            new EntityField("Area", $this->area, "Test Area", fieldSectionType::overview, "text", 250, []),
            new EntityField("IP Address", $this->ipAddress, "10.124.96.XX", fieldSectionType::overview, "text", 250, []),
            new EntityField("MAC Address", $this->macAddress, "", fieldSectionType::specs, "text", 250, []),
            new EntityField("Serial Number", $this->serialNumber, "", fieldSectionType::specs, "text", 250, []),
            new EntityField("Model", $this->model, "", fieldSectionType::specs, "text", 250, []),
            new EntityField("OS", $this->os, "", fieldSectionType::specs, "text", 250, []),
            new EntityField("OS Version", $this->osVersion, "", fieldSectionType::specs, "text", 250, []),
            new EntityField("RAM (GB)", $this->ram_gb, "", fieldSectionType::specs, "text", 250, []),
            new EntityField("Connection Type", $this->connectionType, "", fieldSectionType::connectivity, "text", 250, []),
            new EntityField("Patch Building", $this->patchBuilding, "", fieldSectionType::connectivity, "text", 250, []),
            new EntityField("Patch Number", $this->patchPointNumber, "", fieldSectionType::connectivity, "text", 250, []),
            new EntityField("Patch Switch Port", $this->patchPointSwitchPort, "", fieldSectionType::connectivity, "text", 250, []),
            new EntityField("VLAN ID", $this->vlan_id, "", fieldSectionType::connectivity, "text", 250, []),
            new EntityField("Brief Notes", $this->briefNotes, "", fieldSectionType::overview, "text", 250, []),
            new EntityField("Purpose", $this->purpose, "", fieldSectionType::overview, "text", 250, []),
            new EntityField("Manufacturer", $this->manufacturer, "", fieldSectionType::specs, "text", 250, []),
            new EntityField("Created At", $this->createdAt, "", fieldSectionType::overview, "date", 250, []),
            new EntityField("Status", $this->status, "", fieldSectionType::overview, "select", 250, [])
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