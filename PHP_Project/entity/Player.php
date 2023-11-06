<?php

class Game implements JsonSerializable
{
    private $playerID;
    private $teamID;
    private $firstName;
    private $lastName;
    private $homeTown;
    private $provinceCode;

    public function __construct($playerID, $teamID, $firstName, $lastName, $homeTown, $provinceCode)
    {
        $this->playerID = $playerID;
        $this->teamID = $teamID;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->homeTown = $homeTown;
        $this->provinceCode = $provinceCode;
    }
    public function getPlayerID()
    {
        return $this->playerID;
    }
    public function getTeamId()
    {
        return $this->teamID;
    }
    public function getFirstName()
    {
        return $this->firstName;
    }
    public function getLastName()
    {
        return $this->lastName;
    }
    public function getHomeTown()
    {
        return $this->homeTown;
    }
    public function getProvinceCode()
    {
        return $this->provinceCode;
    }
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
