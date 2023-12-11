<?php

class Team implements JsonSerializable
{
    private $teamID;
    private $teamName;
    public function __construct($teamID, $teamName)
    {
        $this->teamID = $teamID;
        $this->teamName = $teamName;
    }
    public function getTeamID()
    {
        return $this->teamID;
    }
    public function getTeamName()
    {
        return $this->teamName;
    }
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
