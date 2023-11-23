<?php
require_once dirname(__DIR__, 1) . "/entity/Team.php";

class TeamAccessor
{
    private $getAllStatementString = "select * from team";
    private $getByIDStatementString = "select * from team where teamID = :teamID";
    private $updateStatementString = "update Team set teamName = :teamName where teamID = :teamID";

    private $getAllStatement = null;
    private $getByIDStatement = null;
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
            foreach ($dbresults as $r) {
                $teamID = $r['teamID'];
                $teamName = $r['teamName'];

                $obj = new Team($teamID, $teamName);
                array_push($results, $obj);
            }
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
     * @param Integer $id the teamID of the team to retrieve 
     * @return Team Team object with the specified ID, or NULL if not found
     */
    private function getItemByID($id)
    {
        $result = null;

        try {
            $this->getByIDStatement->bindParam(":teamID", $id);
            $this->getByIDStatement->execute();
            $dbresults = $this->getByIDStatement->fetch(PDO::FETCH_ASSOC); // not fetchAll

            if ($dbresults) {
                $teamID = $dbresults['teamID'];
                $teamName = $dbresults['teamName'];

                $result = new Team($teamID, $teamName);
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
     * Does a team exist (with the same ID)?
     * 
     * @param Team $team the item to check
     * @return boolean true if the team exists; false if not
     */
    public function itemExists($team)
    {
        return $this->getItemByID($team->getTeamID()) !== null;
    }

    /**
     * Updates a team name in the database.
     * 
     * @param Team $team an object of type Team, the new values to replace the database's current values
     * @return boolean indicates if the team was updated
     */
    public function updateItem($team)
    {
        if (!$this->itemExists($team)) {
            return false;
        }

        $success = false;

        $teamID = $team->getTeamID();
        $teamName = $team->getTeamName();


        try {
            $this->updateStatement->bindParam(":teamName", $teamName);
            $this->updateStatement->bindParam(":teamID", $teamID);
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