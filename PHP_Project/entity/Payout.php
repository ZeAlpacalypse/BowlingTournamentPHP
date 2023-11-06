<?php

class Payout implements JsonSerializable
{
    private $payoutID;
    private $roundID;
    private $teamID;
    private $amount;

    public function __construct($payoutID, $roundID, $teamID, $amount)
    {
        $this->payoutID = $payoutID;
        $this->roundID = $roundID;
        $this->teamID = $teamID;
        $this->amount = $amount;
    }

    public function getPayoutID()
    {
        return $this->payoutID;
    }

    public function getRoundID()
    {
        return $this->roundID;
    }

    public function getTeamID()
    {
        return $this->teamID;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
