<?php
namespace AirQualityInfo\Model;

class JsonUpdateModel {

    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function logJsonUpdate($deviceId, $ts, $json) {
        $insertStmt = $this->mysqli->prepare("INSERT INTO `json_updates` (`timestamp`, `device_id`, `data`) VALUES (?, ?, ?)");
        $insertStmt->bind_param('iis', $ts, $deviceId, $json);
        $insertStmt->execute();
        $insertStmt->close();

        $before = $ts - 24 * 60 * 60;
        $deleteStmt = $this->mysqli->prepare("DELETE FROM `json_updates` WHERE `timestamp` < ? AND `device_id` = ?");
        $deleteStmt->bind_param('ii', $before, $deviceId);
        $deleteStmt->execute();
        $deleteStmt->close();
    }

    public function getJsonUpdates($deviceId, $limit = PHP_INT_MAX) {
        $result = array();
        $stmt = $this->mysqli->prepare("SELECT `timestamp`, `data` FROM `json_updates` WHERE `device_id` = ? ORDER BY `timestamp` DESC LIMIT ?");
        $stmt->bind_param('ii', $deviceId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_row()) {
            $data[$row[0]] = $row[1];
        }
        $stmt->close();
        return $data;
    }

    public function getJsonUpdate($deviceId, $ts) {
        $stmt = $this->mysqli->prepare("SELECT `data` FROM `json_updates` WHERE `device_id` = ? AND `timestamp` = ?");
        $stmt->bind_param('ii', $deviceId, $ts);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = null;
        if ($row = $result->fetch_row()) {
            $data = $row[0];
        }
        $stmt->close();
        return $data;
    }

    public function getLastJsonUpdate($deviceId) {
        $stmt = $this->mysqli->prepare("SELECT `data`, `timestamp` FROM `json_updates` WHERE `device_id` = ? ORDER by `timestamp` DESC");
        $stmt->bind_param('i', $deviceId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = null;
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $stmt->close();
        return $data;
    }

}
?>