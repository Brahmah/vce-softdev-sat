<?php
require_once './API/Helpers/relativeTime.php';

class ActivityItem
{
    // Properties
    public string $id;
    public string $context;
    public string $item_id;
    public string $date;
    public int $timestamp;
    public string $relative_date;
    public string $body;
    public string $templated_body;
    public array $body_builder;
    public string|null $meta;
    public mixed $param;
    public bool $is_recent;

    // Constructor
    public function __construct(array $rawItem)
    {
        // Set Properties
        $this->id = $rawItem['id'];
        $this->context = $rawItem['context'];
        $this->item_id = $rawItem['item_id'];
        $this->body = $rawItem['body'];
        $this->meta = $rawItem['meta'];
        // Set param property
        /* This looks something like this:
            {
                "device": {
                   "text": "DP-API1",
                   "href": "/devices/12345"
                },
                "status": {
                   "text": "Online"
                }
            }
        */
        $this->param = json_decode($rawItem['param']);
        if ($this->param == null) {
            $this->param = json_decode('{}');
        }
        // Set templated_body property
        $this->templated_body = $this->body;
        if ($this->param != null) {
            foreach ($this->param as $key => $value) {
                $this->templated_body = str_replace(':' . $key, $value->text, $this->templated_body);
            }
        }
        // Set body_builder property
        $this->body_builder = array();
        if ($this->param != null) {
            $body_split_space = explode(' ', $this->body);
            foreach ($body_split_space as $word) {
                if (str_contains($word, ':')) {
                    foreach ($this->param as $key => $value) {
                        if (str_contains($word, ':' . $key)) {
                            $this->body_builder[] = array(
                                'type' => 'param',
                                'text' => trim($value->text, " "),
                                'attr' => $this->param->$key,
                                'id' => hash('md5', $key),
                            );
                        }
                    }
                } else {
                    $this->body_builder[] = array(
                        'type' => 'text',
                        'text' => trim($word, " "),
                        'attr' => null,
                        'id' => hash('md5', $word),
                    );
                }
            }
        }
        // set dates & determine if recent
        $this->timestamp = $rawItem['timestamp'];
        try {
            $item_date = new DateTime($rawItem['date']);
            $this->date = $item_date->format(DateTime::ATOM);
            $this->relative_date = relativeTime($item_date->getTimestamp(), 0);
            if (time() - $item_date->getTimestamp() < 60 * 60 * 24) {
                $this->is_recent = true;
            } else {
                $this->is_recent = false;
            }
        } catch (Exception $e) { // if date is invalid, just give up on life and just use the raw date
            $this->date = "";
            $this->relative_date = $rawItem['date'];
            $this->is_recent = false;
        }
    }
}