<?php
namespace AirQualityInfo\Model;

class JsonUpdateModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function logJsonUpdate($deviceId, $ts, $json) {
        $insertStmt = $this->pdo->prepare("INSERT INTO `json_updates` (`timestamp`, `device_id`, `data`) VALUES (?, ?, ?)");
        $insertStmt->execute([$ts, $deviceId, $json]);
        $insertStmt->closeCursor();

        $before = $ts - 24 * 60 * 60;
        $deleteStmt = $this->pdo->prepare("DELETE FROM `json_updates` WHERE `timestamp` < ? AND `device_id` = ?");
        $deleteStmt->execute([$before, $deviceId]);
        $deleteStmt->closeCursor();
    }

    public function getJsonUpdates($deviceId, $limit = PHP_INT_MAX) {
        $stmt = $this->pdo->prepare("SELECT `timestamp`, `data` FROM `json_updates` WHERE `device_id` = ? ORDER BY `timestamp` DESC LIMIT " . intval($limit));
        $stmt->execute([$deviceId]);
        $data = array();
        while ($row = $stmt->fetch()) {
            $data[$row['timestamp']] = $row['data'];
        }
        $stmt->closeCursor();
        return $data;
    }

    public function getJsonUpdate($deviceId, $ts) {
        $stmt = $this->pdo->prepare("SELECT `data` FROM `json_updates` WHERE `device_id` = ? AND `timestamp` = ?");
        $stmt->execute([$deviceId, $ts]);
        $data = null;
        if ($row = $stmt->fetch()) {
            $data = $row['data'];
        }
        $stmt->closeCursor();
        return $data;
    }

    public function getLastJsonUpdate($deviceId) {
        $stmt = $this->pdo->prepare("SELECT `data`, `timestamp` FROM `json_updates` WHERE `device_id` = ? ORDER by `timestamp` DESC");
        $stmt->execute([$deviceId]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

}
?>