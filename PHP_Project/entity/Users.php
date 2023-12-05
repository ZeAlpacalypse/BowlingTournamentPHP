<?php
class Users implements JsonSerializable
{
    private $userID;
    private $username;
    private $password;
    private $privilegeLevel;
    public function __construct($userID, $username, $password, $privilegeLevel)
    {
        $this->userID = $userID;
        $this->username = $username;
        $this->password = $password;
        $this->privilegeLevel = $privilegeLevel;
    }
    public function getUserID()
    {
        return $this->userID;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function getPrivilegeLevel()
    {
        return $this->privilegeLevel;
    }
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}