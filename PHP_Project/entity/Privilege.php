<?php

class Privilege implements JsonSerializable
{
    private $privilegeLevel;

    public function __construct($privilegeLevel)
    {
        $this->privilegeLevel = $privilegeLevel;
    }
    public function getPrivilegLeve()
    {
        return $this->privilegeLevel;
    }
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
