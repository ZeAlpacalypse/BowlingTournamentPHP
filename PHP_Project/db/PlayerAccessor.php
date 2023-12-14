<?php
require_once dirname(__DIR__, 1) . "/entity/Player.php";

class PlayerAccessor
{
    private $getAllStatementString = "select * from PLAYER";
    private $getByPlayerIDStatementString = "select * from PLAYER where playerID = :playerID";
    private $deleteStatementString = "delete from PLAYER where playerID = :playerID";
    private $insertStatementString = "insert INTO PLAYER values (:playerID, :teamID, :firstName, :lastName, :homeTown, :provinceCode ";
    private $updateStatementString = "update PLAYER  set playerID = :playerID, teamID = :teamID, firstName = :firstName, lastName = :lastName, homeTown = :homeTown, provinceCode = :provinceCode";
    private $getPlayerByTeamString = "select * from PLAYER where teamID = :teamID";

    private $getAllStatement = null;
    private $getByPlayerIDStatement = null;
    private $deleteStatement = null;
    private $insertStatement = null;
    private $updateStatement = null;
    private $getPlayerByTeamStatement = null;

    public function __construct($conn)
    {
        if (is_null($conn)) {
            throw new Exception("no connection");
        }
        $this->getAllStatement = $conn->prepare($this->getAllStatementString);
        if (is_null($this->getAllStatement)) {
            throw new Exception("bad statement: '" . $this->getAllStatementString . "'");
        }
        $this->getByPlayerIDStatement = $conn->prepare($this->getByPlayerIDStatementString);
        if (is_null($this->getByPlayerIDStatement)) {
            throw new Exception("bad statement: '" . $this->getByPlayerIDStatementString . "'");
        }
        $this->deleteStatement = $conn->prepare($this->deleteStatementString);
        if (is_null($this->deleteStatement)) {
            throw new Exception("bad statement: '" . $this->deleteStatementString . "'");
        }
        $this->insertStatement = $conn->prepare($this->insertStatementString);
        if (is_null($this->insertStatement)) {
            throw new Exception("bad statement: '" . $this->insertStatementString . "'");
        }
        $this->updateStatement = $conn->prepare($this->updateStatementString);
        if (is_null($this->updateStatement)) {
            throw new Exception("bad statement: '" . $this->updateStatementString . "'");
        }
        $this->getPlayerByTeamStatement = $conn->prepare($this->getPlayerByTeamString);
        if (is_null($this->getPlayerByTeamStatement)) {
            throw new Exception("bad statement: '" . $this->getPlayerByTeamString . "'");
        }
    }
    /**
     * Gets all the Players
     * 
     * @return Player[] array of Player objects
     */
    public function getAllPlayers()
    {
        $results = [];

        try {
            $this->getAllStatement->execute();
            $dbresults = $this->getAllStatement->fetchAll(PDO::FETCH_ASSOC);
            foreach ($dbresults as $p) {
                $playerID = $p['playerID'];
                $teamID = $p['teamID'];
                $firstName = $p['firstName'];
                $lastName = $p['lastName'];
                $hometown = $p['hometown'];
                $provinceCode = $p['provinceCode'];
                $obj = new Player($playerID, $teamID, $firstName, $lastName, $hometown, $provinceCode);
                array_push($results, $obj);
            }
        } catch (Exception $e) {
            $result = null;
        } finally {
            if (!is_null($this->getByPlayerIDStatement)) {
                $this->getByPlayerIDStatement->closeCursor();
            }
        }
        return $results;
    }

    /**
     * Gets the Player with the specified playerID
     * 
     * @param Integer  $id the ID of the player to retrieve
     * @return Player Player object with the ID, or NULL if not found
     */
    private function getPlayerByID($id)
    {
        $result = null;

        try {
            $this->getByPlayerIDStatement->bindParam(":playerID", $id);
            $this->getByPlayerIDStatement->execute();
            $dbresults = $this->getByPlayerIDStatement->fetch(PDO::FETCH_ASSOC); //Not fetchAll

            if ($dbresults) {
                $playerID = $dbresults['playerID'];
                $teamID = $dbresults['teamID'];
                $firstName = $dbresults['firstName'];
                $lastName = $dbresults['lastName'];
                $homeTown = $dbresults['homeTown'];
                $provinceCode = $dbresults['provinceCode'];
                $result = new Player($playerID, $teamID, $firstName, $lastName, $homeTown, $provinceCode);
            }
        } catch (Exception $e) {
            $result = null;
        } finally {
            if (!is_null($this->getByPlayerIDStatement)) {
                $this->getByPlayerIDStatement->closeCursor();
            }
        }
        return $result;
    }

    /**
     * Does a player exist with the same ID?
     * 
     * @param Player $player the item to check
     * @return boolean true if the item exists; false if not
     */
    public function playerExists($player)
    {
        return $this->getPlayerByID($player->getPlayerID()) !== null;
    }
    /**
     * Deletes a player
     * 
     * @param Player $player an object whose playerID is EQUAL TO the id of the player to delete
     * @return boolean indicates whether the player was deleted
     */
    public function deletePlayer($player)
    {
        if (!$this->playerExists($player)) {
            return false;
        }

        $success = false;
        $playerID = $player->getPlayerID();

        try {
            $this->deleteStatement->bindParam(":playerID", $playerID);
            $success = $this->deleteStatement->execute(); //not what we think
            $success = $success && $this->deleteStatement->rowCount() === 1;
        } catch (PDOException $e) {
            $success = false;
        } finally {
            if (!is_null($this->deleteStatement)) {
                $this->deleteStatement->closeCursor();
            }
        }

        return $success;
    }
    /**
     * Inserts a player into the database.
     * 
     * @param Player $player an object of type Player
     * @return boolean indicated if the item was inserted
     */
    public function insertPlayer($player)
    {
        if ($this->playerExists($player)) {
            return false;
        }

        $success = false;

        $playerID = $player->getPlayerID();
        $teamID = $player->getTeamId();
        $firstName = $player->getFirstName();
        $lastName = $player->getLastName();
        $homeTown = $player->getHomeTown();
        $provinceCode = $player->getProvinceCode();

        try {
            $this->insertStatement->bindParam(":playerID", $playerID);
            $this->insertStatement->bindParam(":teamID", $teamID);
            $this->insertStatement->bindParam(":firstName", $firstName);
            $this->insertStatement->bindParam(":lastName", $lastName);
            $this->insertStatement->bindParam(":homeTown", $homeTown);
            $this->insertStatement->bindParam(":provinceCode", $provinceCode);
            $success = $this->insertStatement->execute(); // this doesn't mean what you think it means
            $success = $this->insertStatement->rowCount() === 1;
        } catch (PDOException $e) {
            $success = false;
        } finally {
            if (!is_null($this->insertStatement)) {
                $this->insertStatement->closeCursor();
            }
        }
        return $success;
    }

    /**
     * Updates a player in the database.
     * 
     * @param Player $player an object of type Player, the new values to replace the db's current values
     * @return boolean indicates if the item was updated
     */
    public function updatePlayer($player)
    {
        if (!$this->playerExists($player)) {
            return false;
        }
        $success = false;

        $playerID = $player->getPlayerID();
        $teamID = $player->getTeamId();
        $firstName = $player->getFirstName();
        $lastName = $player->getLastName();
        $homeTown = $player->getHomeTown();
        $provinceCode = $player->getProvinceCode();

        try {
            $this->updateStatement->bindParam(":playerID", $playerID);
            $this->updateStatement->bindParam(":teamID", $teamID);
            $this->updateStatement->bindParam(":firstName", $firstName);
            $this->updateStatement->bindParam(":lastName", $lastName);
            $this->updateStatement->bindParam(":homeTown", $homeTown);
            $this->updateStatement->bindParam(":provinceCode", $provinceCode);
            $success = $this->updateStatement->execute(); //not what we think
            $success = $success && $this->updateStatement->rowCount() === 1;
        } catch (PDOException $e) {
            $success = false;
        } finally {
            if (!is_null($this->updateStatement)) {
                $this->updateStatement->closeCursor();
            }
        }
        return $success;
    }
    /**
     * Gets the Player with the specified teamID
     * 
     * @param Integer  $id the teamID of the player to retrieve
     * @return Player[] Player object with the ID, or NULL if not found
     */
    public function getPlayerByTeamID($id)
    {
        $results = [];

        try {
            $this->getPlayerByTeamStatement->bindParam(":teamID", $id);
            $this->getPlayerByTeamStatement->execute();
            $dbresults = $this->getPlayerByTeamStatement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dbresults as $p) {
                $playerID = $p['playerID'];
                $teamID = $p['teamID'];
                $firstName = $p['firstName'];
                $lastName = $p['lastName'];
                $homeTown = $p['homeTown'];
                $provinceCode = $p['provinceCode'];
                $obj = new Player($playerID, $teamID, $firstName, $lastName, $homeTown, $provinceCode);
                array_push($results, $obj);
            }
        } catch (Exception $e) {
            $results = null;
        } finally {
            if (!is_null($this->getPlayerByTeamStatement)) {
                $this->getPlayerByTeamStatement->closeCursor();
            }
        }
        return $results;
    }
}
//end class PlayerAccessor