<?php
require_once dirname(__DIR__, 1) . '/db/ConnectionManager.php';
require_once dirname(__DIR__, 1) . '/db/GameAccessor.php';
require_once dirname(__DIR__, 1) . '/entity/Game.php';
require_once dirname(__DIR__, 1) . '/utils/Constants.php';
require_once dirname(__DIR__, 1) . '/utils/ChromePhp.php';


$method = $_SERVER['REQUEST_METHOD'];

try {
    $cm = new ConnectionManager(Constants::$MYSQL_CONNECTION_STRING, Constants::$MYSQL_USERNAME, Constants::$MYSQL_PASSWORD);
    $ga = new GameAccessor($cm->getConnection());

    if ($method === "GET") {
        doGet($ga);
    } else if ($method === "POST") {
        doPost($ga);
    } else if ($method === "DELETE") {
        doDelete($ga);
    } else if ($method === "PUT") {
        doPut($ga);
    } else {
        sendResponse(405, null, "method not allowed");
    }
} catch (Exception $e) {
    sendResponse(500, null, "ERROR " . $e->getMessage());
} finally {
    if (!is_null($cm)) {
        $cm->closeConnection();
    }
}
function doGet($ga)
{
    // individual
    if (isset($_GET['gameid'])) {

        sendResponse(405, null, "individual GETs not allowed");
    }
    // collection
    else {
        try {
            $results = $ga->getAllItems();
            if (count($results) > 0) {
                $results = json_encode($results, JSON_NUMERIC_CHECK);
                sendResponse(200, $results, null);
            } else {
                sendResponse(404, null, "could not retrieve items");
            }
        } catch (Exception $e) {
            sendResponse(500, null, "ERROR " . $e->getMessage());
        }
    }
}

function doDelete($ga)
{
    if (isset($_GET['teamid'])) {
        $teamID = $_GET['teamid'];
        // Only the ID of the item matters for a delete,
        // but the accessor expects an object, 
        // so we need a dummy object.
        $teamObj = new Team($teamID, "dummyName");

        // delete the object from DB
        $success = $ga->deleteItem($teamObj);
        if ($success) {
            sendResponse(200, $success, null);
        } else {
            sendResponse(404, null, "could not delete item - it does not exist");
        }
    } else {

        // Bulk deletes not implemented.
        sendResponse(405, null, "bulk DELETEs not allowed");
    }
}

// aka CREATE
function doPost($ga)
{
    if (isset($_GET['teamid'])) {
        // The details of the item to insert will be in the request body.
        $body = file_get_contents('php://input');
        $contents = json_decode($body, true);

        try {
            // create a Team object
            $teamObj = new Team($contents['teamID'], $contents['teamName']);

            // add the object to DB
            $success = $ga->insertItem($teamObj);
            if ($success) {
                sendResponse(201, $success, null);
            } else {
                sendResponse(409, null, "could not insert item - it already exists");
            }
        } catch (Exception $e) {
            sendResponse(400, null, $e->getMessage());
        }
    } else {
        // Bulk inserts not implemented.
        sendResponse(405, null, "bulk INSERTs not allowed");
    }
}

// aka UPDATE
function doPut($ga)
{
    if (isset($_GET['teamid'])) {
        // The details of the item to update will be in the request body.
        $body = file_get_contents('php://input');
        $contents = json_decode($body, true);

        try {
            // create a Team object
            $teamObj = new Team($contents['teamID'], $contents['teamName']);
            // update the object in the  DB
            $success = $ga->updateItem($teamObj);
            if ($success) {
                sendResponse(200, $success, null);
            } else {
                sendResponse(404, null, "could not update item - it does not exist");
            }
        } catch (Exception $e) {
            sendResponse(400, null, $e->getMessage());
        }
    } else {
        // Bulk updates not implemented.
        sendResponse(405, null, "bulk UPDATEs not allowed");
    }
}
function sendResponse($statusCode, $data, $error)
{
    header("Content-Type: application/json");
    http_response_code($statusCode);
    $resp = ['data' => $data, 'error' => $error];
    echo json_encode($resp, JSON_NUMERIC_CHECK);
}
