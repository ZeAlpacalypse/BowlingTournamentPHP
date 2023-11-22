<?php
require_once dirname(__DIR__, 1) . '/entity/Province.php';

class ProvinceAccessor
{
    private $getAllStatementString = "select * from province";
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
    /**
     * Gets all menu item categories.
     * 
     * @return array GamePlatform objects, possibly empty
     */
    public function getAllCategories()
    {
        $result = [];

        try {
            $this->getAllStatement->execute();
            $dbresults = $this->getAllStatement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dbresults as $r) {
                $platformID = $r['platformID'];
                $platformDescription = $r['platformDescription'];
                $obj = new Province($platformID, $platformDescription);
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