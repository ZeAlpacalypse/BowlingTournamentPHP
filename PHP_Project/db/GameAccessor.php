<?php
require_once dirname(__DIR__, 1) . "/entity/Game.php";

class GameAccessor
{
    //All insert statements are commented out because I don't think we will need them but if we do they can be implemented.
    private $getAllStatementString = "select * from Game";
    private $getByIDStatementString = "select * from Game where gameID = :gameID";


    // private $insertStatementString = "insert INTO Game values (:gameID, :matchID, :gameNumber, :gameStateID, :score, :balls, :playerID";
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




        // $this->insertStatement = $conn->prepare($this->insertStatementString);
        // if (is_null($this->insertStatement)) {
        //     throw new Exception("bad statement: '" . $this->getAllStatementString . "'");
        // }

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

    /**
     * Does an item exist (with the same ID)?
     * 
     * @param Game $item the item to check
     * @return boolean true if the item exists; false if not
     */
    public function itemExists($item)
    {
        return $this->getItemByID($item->getgameID()) !== null;
    }
    /**
     * Inserts a menu item into the database.
     * 
     * @param Game $item an object of type Game
     * @return boolean indicates if the item was inserted
     */

    //This is commented out because I don't believe we need to add more games and are 
    //just updating ones that are already there when scoring a game

    // public function insertItem($item)
    // {
    //     if ($this->itemExists($item)) {
    //         return false;
    //     }

    //     $success = false;

    //     $gameID = $item->getgameID();
    //     $matchID = $item->getMatchID();
    //     $gameNumber = $item->getGameNumber();
    //     $gameStateID = $item->getGameStateID();
    //     $score = $item->getScore();
    //     $balls = $item->getBalls();
    //     $playerID = $item->getPlayerID();
    //     try {
    //         $this->insertStatement->bindParam(":gameID", $gameID);
    //         $this->insertStatement->bindParam(":matchID", $matchID);
    //         $this->insertStatement->bindParam(":gameNumber", $gameNumber);
    //         $this->insertStatement->bindParam(":gameStateID", $gameStateID);
    //         $this->insertStatement->bindParam(":score", $score);
    //         $this->insertStatement->bindParam(":balls", $balls);
    //         $this->insertStatement->bindParam(":playerID", $playerID);
    //         $success = $this->insertStatement->execute(); // this doesn't mean what you think it means
    //         $success = $this->insertStatement->rowCount() === 1;
    //     } catch (PDOException $e) {
    //         $success = false;
    //     } finally {
    //         if (!is_null($this->insertStatement)) {
    //             $this->insertStatement->closeCursor();
    //         }
    //     }
    //     return $success;
    // }
    /**
     * Updates a menu item in the database.
     * 
     * @param Game $item an object of type CatalogGames, the new values to replace the database's current values
     * @return boolean indicates if the item was updated
     */
    public function updateItem($item)
    {
        if (!$this->itemExists($item)) {
            return false;
        }

        $success = false;

        $gameID = $item->getgameID();
        $matchID = $item->getMatchID();
        $gameNumber = $item->getGameNumber();
        $gameStateID = $item->getGameStateID();
        $score = $item->getScore();
        $balls = $item->getBalls();
        $playerID = $item->getPlayerID();

        try {
            $this->updateStatement->bindParam(":gameID", $gameID);
            $this->updateStatement->bindParam(":matchID", $matchID);
            $this->updateStatement->bindParam(":gameNumber", $gameNumber);
            $this->updateStatement->bindParam(":gameStateID", $gameStateID);
            $this->updateStatement->bindParam(":score", $score);
            $this->updateStatement->bindParam(":balls", $balls);
            $this->updateStatement->bindParam(":playerID", $playerID);


            $success = $this->updateStatement->execute(); // this doesn't mean what you think it means
            $success = $this->updateStatement->rowCount() === 1;
        } catch (PDOException $e) {
            $success = false;
        } finally {
            if (!is_null($this->updateStatement)) {
                $this->updateStatement->closeCursor();
            }
        }
        return $success;
    }
}