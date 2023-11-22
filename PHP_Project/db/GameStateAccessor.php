<?php
require_once dirname(__DIR__, 1) . "/entity/GameState.php";

class GameStateAccessor
{
    private $getAllStatementString = "select * from GameState";
    private $getAllStatement = null;

    public function __construct($conn)
    {
        if (is_null($conn)) {
            throw new Exception("no connection");
        }

        $this->getAllStatement = $conn->prepare($this->getAllStatementString);
        if (is_null($this->getAllStatement)) {
            throw new Exception("bad statement: '" . $this->getAllStatementString . "'");
        }
    }
    public function getAllGameStates()
    {
        $result = [];

        try {
            $this->getAllStatement->execute();
            $dbresults = $this->getAllStatement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dbresults as $r) {
                $gameStateID = $r['gameStateID'];

                $obj = new GameState($gameStateID);
                array_push($result, $obj);
            }
        } catch (Exception $e) {
            $result = [];
        } finally {
            if (!is_null($this->getAllStatement)) {
                $this->getAllStatement->closeCursor();
            }
        }

        return $result;
    }
}