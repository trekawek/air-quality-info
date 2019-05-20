<?php
namespace AirQualityInfo\Model;

class DeviceModel {

    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function getDevicesForUser($userId) {
        $stmt = $this->mysqli->prepare("SELECT * FROM `devices` WHERE `user_id` = ? ORDER BY `position`");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    public function getMappingForDevice($deviceId) {
        $stmt = $this->mysqli->prepare("SELECT `db_name`, `json_name` FROM `device_mapping` WHERE `device_id` = ?");
        $stmt->bind_param('i', $deviceId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $dbName = $row['db_name'];
            $jsonName = $row['json_name'];
            if (!isset($data[$dbName])) {
                $data[$dbName] = array();
            }
            $data[$dbName][] = $jsonName;
        }
        $stmt->close();
        return $data;

    }

}
?>