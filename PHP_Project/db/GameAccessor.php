<?php
require_once dirname(__DIR__, 1) . "/entity/Game.php";

class GameAccessor
{
    private $getAllStatementString = "select * from Game";
    private $getByIDStatementString = "select * from CatalogGames where gameID = :gameID";
    private $deleteStatementString = "delete from CatalogGames where gameID = :gameID";
    private $insertStatementString = "insert INTO Game values (:gameID, :matchID, :gameNumber, :gameStateID, :score, :balls, :playerID";
    private $updateStatementString = "update Game set matchID = :matchID, gameNumber = :gameNumber, gameStateID = :gameStateID, score = :score, balls = :balls, playerID = :playerID where gameID = :gameID";
    private $getAllStatement = null;
    private $getByIDStatement = null;
    private $deleteStatement = null;
    private $insertStatement = null;
    private $updateStatement = null;

    public function __construct($conn)
    {
        if (is_null($conn)) {
            throw new Exception("no connection");
        }

        $this->getAllStatement = $conn->prepare($this->getAllStatementString);
        if (is_null($this->getAllStatement)) {
            throw new Exception("bad statement: '" . $this->getAllStatementString . "'");
        }

        $this->getByIDStatement = $conn->prepare($this->getByIDStatementString);
        if (is_null($this->getByIDStatement)) {
            throw new Exception("bad statement: '" . $this->getByIDStatementString . "'");
        }

        $this->deleteStatement = $conn->prepare($this->deleteStatementString);
        if (is_null($this->deleteStatement)) {
            throw new Exception("bad statement: '" . $this->deleteStatementString . "'");
        }

        $this->insertStatement = $conn->prepare($this->insertStatementString);
        if (is_null($this->insertStatement)) {
            throw new Exception("bad statement: '" . $this->getAllStatementString . "'");
        }

        $this->updateStatement = $conn->prepare($this->updateStatementString);
        if (is_null($this->updateStatement)) {
            throw new Exception("bad statement: '" . $this->updateStatementString . "'");
        }
    }

    public function getAllItems()
    {
        $results = [];
        try {
            $this->getAllStatement->execute();
            $dbresults = $this->getAllStatement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dbresults as $row) {
                $gameID = $row['gameID'];
                $matchID = $row['matchID'];
                $gameNumber = $row['gameNumber'];
                $gameStateID = $row['gameStateID'];
                $score = $row['score'];
                $balls = $row['balls'];
                $playerID = $row['playerID'];
                $obj = new Game($gameID, $matchID, $gameNumber, $gameStateID, $score, $balls, $playerID);
                array_push($results, $obj);
            }
            //code...
        } catch (Exception $e) {
            $results = [];
        } finally {
            if (!is_null($this->getAllStatement)) {
                $this->getAllStatement->closeCursor();
            }
        }
        return $results;
    }
    /**
     * Gets the menu item with the specified ID.
     * 
     * @param Integer $id the ID of the item to retrieve 
     * @return Game Game object with the specified ID, or NULL if not found
     */
    private function getItemByID($id)
    {
        $result = null;

        try {
            $this->getByIDStatement->bindParam(":gameID", $id);
            $this->getByIDStatement->execute();
            $dbresults = $this->getByIDStatement->fetch(PDO::FETCH_ASSOC); // not fetchAll

            if ($dbresults) {
                $gameID = $dbresults['gameID'];
                $matchID = $dbresults['matchID'];
                $gameNumber = $dbresults['gameNumber'];
                $gameStateID = $dbresults['gameStateID'];
                $score = $dbresults['score'];
                $balls = $dbresults['balls'];
                $playerID = $dbresults['playerID'];
                $result = new Game($gameID, $matchID, $gameNumber, $gameStateID, $score, $balls, $playerID);
            }
        } catch (Exception $e) {
            $result = null;
        } finally {
            if (!is_null($this->getByIDStatement)) {
                $this->getByIDStatement->closeCursor();
            }
        }

        return $result;
    }
}