<?php

class Area
{
    // Properties
    public string $id; // String because of frontend bug: https://github.com/bartaxyz/react-tree-list/issues/28
    public string $parent_id;
    public string $label;
    public string $description;
    public string $type;
    // Extra properties
    public string $icon;

    // Constructor
    /**
     * @param array $rawDbArea - Raw db result
     * @param bool $isInnerJoin - will use an alternative set of key names. eg: area_name
     * instead of just name
     */
    public function __construct(array $rawDbArea, bool $isInnerJoin)
    {
        // Set properties
        if ($isInnerJoin) {
            $this->id = (string)$rawDbArea['area_id'];
            $this->parent_id = (string)$rawDbArea['area_parent_id'];
            $this->label = $rawDbArea['area_name'];
            $this->description = $rawDbArea['area_description'];
            $this->type = $rawDbArea['area_type'];
        } else {
            $this->id = (string)$rawDbArea['id'];
            $this->parent_id = (string)$rawDbArea['parent_id'];
            $this->label = $rawDbArea['name'];
            $this->description = $rawDbArea['description'];
            $this->type = $rawDbArea['type'];
        }
        // Set extra properties
        $this->icon = match ($this->type) {
            'campus' => '🏫',
            'building' => '🏛',
            'room' => '🚪',
            'cave' => '⛰️',
            default => '📁',
        };
    }
}