<?php

class Game implements JsonSerializable
{
    private $gameID;
    private $matchID;
    private $gameNumber;
    private $gameStateID;
    private $score;
    private $balls;
    private $playerID;

    public function __construct($gameID, $matchID, $gameNumber, $gameStateID, $score, $balls, $playerID)
    {
        $this->gameID = $gameID;
        $this->matchID = $matchID;
        $this->gameNumber = $gameNumber;
        $this->gameStateID = $gameStateID;
        $this->score = $score;
        $this->balls = $balls;
        $this->playerID = $playerID;
    }

    public function getgameID()
    {
        return $this->gameID;
    }

    public function getmatchID()
    {
        return $this->matchID;
    }

    public function getgameNumber()
    {
        return $this->gameNumber;
    }

    public function getgameStateID()
    {
        return $this->gameStateID;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
// end class CatalogGames