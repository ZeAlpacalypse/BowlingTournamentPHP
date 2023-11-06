<?php
require_once dirname(__DIR__, 1) . "/entity/Game.php";

class GameAccessor
{
    private $getAllStatementString = "select * from Game";
    private $getByIDStatementString = "select * from CatalogGames where gameID = :gameID";
    private $deleteStatementString = "delete from CatalogGames where gameID = :gameID";
    private $insertSStatementString = "insert INTO Game values (:gameID, :matchID, :gameNumber, :gameStateID, :score, :balls, :playerID";
    private $updateSStatementString = "update Game set matchID = :matchID, gameNumber = :gameNumber, gameStateID = :gameStateID, score = :score, balls = :balls, playerID = :playerID where gameID = :gameID";
    private $getAllStatement = null;
    private $getByIDStatement = null;
    private $deleteStatement = null;
    private $insertStatement = null;
    private $updateStatement = null;
}