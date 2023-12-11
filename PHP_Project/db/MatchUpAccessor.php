<?php
require_once dirname(__DIR__, 1) . "/entity/MatchUp.php";

class MatchUpAccessor
{
    private $getAllStatementString = "select * from MATCHUP";
    private $getByIDStatementString = "select * from MATCHUP where matchID = :matchID";
    private $deleteStatementString = "delete from MATCHUP where matchID = :matchID";
    private $insertStatementString = "insert INTO MATCHUP values (:matchID, :roundID, :matchgroup, :teamID, :score, :ranking";
    private $updateStatementString = "update MATCHUP set roundID = :roundID, matchgroup = :matchgroup, teamID = :teamID, score = :score, ranking = :ranking where matchID = :matchID";
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

    /**
     * Gets all of the matchups
     * 
     * @return MatchUp[] array of MatchUp objects
     */
    public function getAllMatches()
    {
        $results = [];

        try {
            $this->getAllStatement->execute();
            $dbresults = $this->getAllStatement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dbresults as $r) {
                $matchID = $r['matchID'];
                $roundID = $r['roundID'];
                $matchgroup = $r['matchgroup'];
                $teamID = $r['teamID'];
                $score = $r['score'];
                $ranking = $r['ranking'];
                $obj = new MatchUp($matchID, $roundID, $matchgroup, $teamID, $score, $ranking);
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
    } //end getAllMatches

    /**
     * Gets the matchup with the specified ID.
     * 
     * @param Integer $id the ID of the item to retrieve 
     * @return MatchUp Matchup object with the specified ID, or NULL if not found
     */
    private function getMatchByID($id)
    {
        $result = null;

        try {
            $this->getByIDStatement->bindParam(":matchID", $id);
            $this->getByIDStatement->execute();
            $dbresults = $this->getByIDStatement->fetch(PDO::FETCH_ASSOC); // not fetchAll

            if ($dbresults) {
                $matchID = $dbresults['matchID'];
                $roundID = $dbresults['roundID'];
                $matchgroup = $dbresults['matchgroup'];
                $teamID = $dbresults['teamID'];
                $score = $dbresults['score'];
                $ranking = $dbresults['ranking'];
                $result = new MatchUp($matchID, $roundID, $matchgroup, $teamID, $score, $ranking);
            }
        } catch (Exception $e) {
            $result = null;
        } finally {
            if (!is_null($this->getByIDStatement)) {
                $this->getByIDStatement->closeCursor();
            }
        }

        return $result;
    } //end getMatchByID

    /**
     * Does a match exist (with the same ID)?
     * 
     * @param MatchUp $match the match to check
     * @return boolean true if the match exists; false if not
     */
    public function matchExists($match)
    {
        return $this->getMatchByID($match->getMatchID()) !== null;
    } //end matchExists

    /**
     * Inserts a match into the database.
     * 
     * @param MatchUp $match an object of type MatchUp
     * @return boolean indicates if the match was inserted
     */
    public function insertMatch($match)
    {
        if ($this->matchExists($match)) {
            return false;
        }

        $success = false;

        $matchID = $match->getMatchID();
        $roundID = $match->getRoundID();
        $matchgroup = $match->getMatchGroup();
        $teamID = $match->getTeamID();
        $score = $match->getScore();
        $ranking = $match->getRanking();

        try {
            $this->insertStatement->bindParam(":matchID", $matchID);
            $this->insertStatement->bindParam(":roundID", $roundID);
            $this->insertStatement->bindParam(":matchgroup", $matchgroup);
            $this->insertStatement->bindParam(":teamID", $teamID);
            $this->insertStatement->bindParam(":score", $score);
            $this->insertStatement->bindParam(":ranking", $ranking);
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
    } //end insertMatch

    /**
     * Updates a match in the database.
     * 
     * @param MatchUp $match an object of type MatchUp, the new values to replace the database's current values
     * @return boolean indicates if the match was updated
     */
    public function updateMatch($match)
    {
        if ($this->matchExists($match)) {
            return false;
        }

        $success = false;

        $matchID = $match->getMatchID();
        $roundID = $match->getRoundID();
        $matchgroup = $match->getMatchGroup();
        $teamID = $match->getTeamID();
        $score = $match->getScore();
        $ranking = $match->getRanking();

        try {
            $this->updateStatement->bindParam(":matchID", $matchID);
            $this->updateStatement->bindParam(":roundID", $roundID);
            $this->updateStatement->bindParam(":matchgroup", $matchgroup);
            $this->updateStatement->bindParam(":teamID", $teamID);
            $this->updateStatement->bindParam(":score", $score);
            $this->updateStatement->bindParam(":ranking", $ranking);
            $success = $this->updateStatement->execute(); // this doesn't mean what you think it means
            $success = $this->updateStatement->rowCount() === 1;
        } catch (PDOException $e) {
            $success = false;
        } finally {
            if (!is_null($this->insertStatement)) {
                $this->insertStatement->closeCursor();
            }
        }
        return $success;
    } //end updateMatch

    /**
     * Deletes a matchup.
     * 
     * @param MatchUp $match an object whose ID is EQUAL TO the ID of the match to delete
     * @return boolean indicates whether the item was deleted
     */
    public function deleteMatch($match)
    {
        if (!$this->matchExists($match)) {
            return false;
        }

        $success = false;
        $matchID = $match->getMatchID(); // only the ID is needed

        try {
            $this->deleteStatement->bindParam(":matchID", $matchID);
            $success = $this->deleteStatement->execute(); // this doesn't mean what you think it means
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
}//end MatchUpAccessor Class
