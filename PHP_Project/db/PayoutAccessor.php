<?php
require_once dirname(__DIR__, 1) . "/entity/Payout.php";
class PayoutAccessor
{
    private $getAllStatementString = "select * from PAYOUT";
    private $getByPayIDStatementString = "select * from PAYOUT where payoutID = :payoutID";
    private $deleteStatementString = "delete from PAYOUT where payoutID = :payoutID";
    private $insertStatementString = "insert INTO PAYOUT values (:payoutID, :roundID, :teamID, :amount";
    private $updateStatementString = "update PAYOUT set payoutID = :payoutID, roundID = :roundID, teamID = :teamID, amount = :amount";
    private $getAllStatement = null;
    private $getByPayIDStatement = null;
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
        $this->getByPayIDStatement = $conn->prepare($this->getByPayIDStatementString);
        if (is_null($this->getByPayIDStatement)) {
            throw new Exception("bad statement: '" . $this->getByPayIDStatementString . "'");
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
    }
    /**
     * Gets all the Payout
     * 
     * @return Payout[] array of Player objects
     */
    public function getAllPayouts()
    {
        $results = [];

        try {
            $this->getAllStatement->execute();
            $dbresults = $this->getAllStatement->fetchAll(PDO::FETCH_ASSOC);
            foreach ($dbresults as $p) {
                $payoutID = $p['payoutID'];
                $roundID = $p['roundID'];
                $teamID = $p['teamID'];
                $amount = $p['amount'];

                $obj = new Payout($payoutID, $roundID, $teamID, $amount);
                array_push($results, $obj);
            }
        } catch (Exception $e) {
            $result = null;
        } finally {
            if (!is_null($this->getByPayIDStatement)) {
                $this->getByPayIDStatement->closeCursor();
            }
        }
        return $results;
    }
    /**
     * Gets the Payout with the specified payoutID
     * 
     * @param Integer  $id the ID of the payout to retrieve
     * @return Payout Payout object with the ID, or NULL if not found
     */
    private function getPayoutByID($id)
    {
        $result = null;
        try {
            $this->getByPayIDStatement->bindParam(":payoutID", $id);
            $this->getByPayIDStatement->execute();
            $dbresults = $this->getByPayIDStatement->fetch(PDO::FETCH_ASSOC); //Not FetchAll

            if ($dbresults) {
                $payoutID = $dbresults['payoutID'];
                $roundID = $dbresults['roundID'];
                $teamID = $dbresults['teamID'];
                $amount = $dbresults['amount'];
                $result = new Payout($payoutID, $roundID, $teamID, $amount);
            }
        } catch (Exception $e) {
            $result = null;
        } finally {
            if (!is_null($this->getByPayIDStatement)) {
                $this->getByPayIDStatement->closeCursor();
            }
        }
        return $result;
    }
    /**
     * Does a payout exist with the same ID?
     * 
     * @param Payout $payout the item to check
     * @return boolean true if the item exists; false if not
     */
    public function payoutExists($payout)
    {
        return $this->getPayoutByID($payout->getPayoutID()) !== null;
    }
    /**
     * Deletes a payout
     * 
     * @param Payout $payout an object whose payoutID is EQUAL TO the id of the payout to delete
     * @return boolean indicates whether the payout was deleted
     */
    public function deletePayout($payout)
    {
        if (!$this->payoutExists($payout)) {
            return false;
        }

        $success = false;
        $payoutID = $payout->getPayoutID();

        try {
            $this->deleteStatement->bindParam(":payoutID", $payoutID);
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
     * Inserts a payout into the database.
     * 
     * @param Payout $payout an object of type payout
     * @return boolean indicated if the item was inserted
     */
    public function insertPayout($payout)
    {
        if ($this->payoutExists($payout)) {
            return false;
        }

        $success = false;

        $payoutID = $payout->getPayoutID();
        $roundID = $payout->getRoundID();
        $teamID = $payout->getTeamId();
        $amount = $payout->getAmount();

        try {
            $this->insertStatement->bindParam(":payoutID", $payoutID);
            $this->insertStatement->bindParam(":roundID", $roundID);
            $this->insertStatement->bindParam(":teamID", $teamID);
            $this->insertStatement->bindParam(":amount", $amount);
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
     * Updates a payout in the database.
     * 
     * @param Payout $payout an object of type Payout, the new values to replace the db's current values
     * @return boolean indicates if the item was updated
     */
    public function updatePayout($payout)
    {
        if (!$this->payoutExists($payout)) {
            return false;
        }
        $success = false;

        $payoutID = $payout->getPayoutID();
        $roundID = $payout->getRoundID();
        $teamID = $payout->getTeamId();
        $amount = $payout->getAmount();

        try {
            $this->updateStatement->bindParam(":payoutID", $payoutID);
            $this->updateStatement->bindParam(":roundID", $roundID);
            $this->updateStatement->bindParam(":teamID", $teamID);
            $this->updateStatement->bindParam(":amount", $amount);
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
}
