<?php
require_once dirname(__DIR__, 1) . "/entity/GameState.php";

class GameStateAccessor
{
    private $getAllStatementString = "select * from GameState";
    private $getAllStatement = "";
}