<?php
require_once dirname(__DIR__, 1) . "/entity/Users.php";

class UsersAccessor
{
    private $getUserStatementString = "select password from Users where username = :username";
    private $getUserStatement = null;
    private $getUserExistsStatementString = "select * from Users where username = :username";
    private $getUserExistsStatement = null;

    public function __construct($conn)
    {
        if (is_null($conn)) {
            throw new Exception("no connection");
        }

        $this->getUserStatement = $conn->prepare($this->getUserStatementString);
        if (is_null($this->getUserStatement)) {
            throw new Exception("bad statement: '" . $this->getUserStatementString . "'");
        }
        $this->getUserExistsStatement = $conn->prepare($this->getUserExistsStatementString);
        if (is_null($this->getUserExistsStatement)) {
            throw new Exception("bad statement '" . $this->getUserExistsStatementString . "'");
        }
    }
    /* public function loginUser($username, $password)
    {
        $results = ["message" => "", "bool" => false];
        if (!userExists($username)) {
            $results["Message"] = "User does not exist";
            return $results;
        }
        try {
            $this->getUserStatement->bindParam(":username", $username);
            $this->getUserStatement->execute();
            $dbresults = $this->getUserStatement->fetch(PDO::FETCH_ASSOC);

            if ($dbresults) {
                $dbPassword = $dbresults['password'];
                if ($dbPassword == $password) {
                    $results["bool"] = true;
                }
            }
        } catch (Exception $e) {
            $results["Message"] = "Error getting data from the database";
        } finally {
            if (!is_null($this->getUserStatement)) {
                $this->getUserStatement->closeCursor();
            }
        }
        return $results;
    }*/
    private function userExists($username)
    {
        return $this->getUser($username) !== null;
    }
    private function getUser($user)
    {
        $result = null;
        try {
            $this->getUserExistsStatement->bindParam(":username", $user);
            $this->getUserExistsStatement->execute();
            $dbres = $this->getUserExistsStatement->fetch(PDO::FETCH_ASSOC);
            if ($dbres) {
                $userID = $dbres["userID"];
                $username = $dbres["username"];
                $password = $dbres["password"];
                $privilege = $dbres["privilegeLevel"];
                $result = new Users($userID, $username, $password, $privilege);
            }
        } catch (Exception $e) {
            $result = null;
        } finally {
            if (!is_null($this->getUserExistsStatement)) {
                $this->getUserExistsStatement->closeCursor();
            }
        }
        return $result;
    }
}
