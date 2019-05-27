<?php
namespace AirQualityInfo\Model;

class DeviceModel {

    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function createDevice($data) {
        $sql = "INSERT INTO `devices` ( ";
        foreach ($data as $k => $v) {
            $sql .= "`$k`, ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= ") VALUES (";
        $sql .= str_repeat('?, ', count($data));
        $sql = substr($sql, 0, -2);
        $sql .= ")";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($data)), ...array_values($data));    
        $stmt->execute();
        return $this->mysqli->insert_id;
    }

    public function getDeviceById($deviceId) {
        $stmt = $this->mysqli->prepare("SELECT * FROM `devices` WHERE `id` = ?");
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

    public function getDevicesForUser($userId) {
        $stmt = $this->mysqli->prepare("SELECT * FROM `devices` WHERE `user_id` = ? ORDER BY `id`");
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

    public function deleteDevice($deviceId) {
        $stmt = $this->mysqli->prepare("DELETE FROM `devices` WHERE `id` = ?");
        $stmt->bind_param('i', $deviceId);
        $stmt->execute();
    }

    public function addMapping($deviceId, $dbName, $jsonName) {
        $stmt = $this->mysqli->prepare("INSERT INTO `device_mapping` (`device_id`, `db_name`, `json_name`) VALUES (?, ?, ?)");
        $stmt->bind_param('iss', $deviceId, $dbName, $jsonName);
        $stmt->execute();
    }

    public function deleteMapping($deviceId, $mappingId) {
        $stmt = $this->mysqli->prepare("DELETE FROM `device_mapping` WHERE `device_id` = ? AND `id` = ?");
        $stmt->bind_param('ii', $deviceId, $mappingId);
        $stmt->execute();
    }

    public function getMappingForDevice($deviceId) {
        $stmt = $this->mysqli->prepare("SELECT `id`, `db_name`, `json_name` FROM `device_mapping` WHERE `device_id` = ?");
        $stmt->bind_param('i', $deviceId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    public function updateDevice($deviceId, $data) {
        $sql = "UPDATE `devices` SET ";
        foreach ($data as $k => $v) {
            $sql .= "`$k` = ?, ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE `id` = ?";

        $stmt = $this->mysqli->prepare($sql);
        $params = array_values($data);
        $params[] = $deviceId;
        $stmt->bind_param(str_repeat('s', count($data)).'i', ...$params);    
        $stmt->execute();
    }

}
?>