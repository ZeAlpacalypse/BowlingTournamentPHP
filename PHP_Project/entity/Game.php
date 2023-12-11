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

    public function getGameID()
    {
        return $this->gameID;
    }

    public function getMatchID()
    {
        return $this->matchID;
    }

    public function getGameNumber()
    {
        return $this->gameNumber;
    }

    public function getGameStateID()
    {
        return $this->gameStateID;
    }

    public function getScore()
    {
        return $this->score;
    }
    public function getBalls()
    {
        return $this->balls;
    }
    public function getPlayerID()
    {
        return $this->playerID;
    }
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
// end class CatalogGames