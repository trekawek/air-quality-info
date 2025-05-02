<?php
namespace AirQualityInfo\Model;

class DeviceModel {

    private $pdo;

    private $userModel;

    public function __construct($pdo, UserModel $userModel) {
        $this->pdo = $pdo;
        $this->userModel = $userModel;
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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        $stmt->closeCursor();
        return $this->pdo->lastInsertId();
    }

    public function getDeviceById($deviceId) {
        $stmt = $this->pdo->prepare("SELECT * FROM `devices` WHERE `id` = ?");
        $stmt->execute([$deviceId]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    public function getDeviceByApiKey($apiKey) {
        $stmt = $this->pdo->prepare("SELECT * FROM `devices` WHERE `api_key` = ?");
        $stmt->execute([$apiKey]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    public function getDeviceByAssignToken($assignToken) {
        $stmt = $this->pdo->prepare("SELECT * FROM `devices` WHERE `assign_token` = ?");
        $stmt->execute([$assignToken]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    public function getDeviceByUserAndTtnId($userId, $ttnId) {
        $stmt = $this->pdo->prepare("SELECT * FROM `devices` WHERE `user_id` = ? AND (`name` = ? OR `ttn_id` = ?)");
        $stmt->execute([$userId, $ttnId, $ttnId]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    public function getDevicesForUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM `devices` WHERE `user_id` = ? ORDER BY `default_device` DESC, `id` ASC");
        $stmt->execute([$userId]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $user = $this->userModel->getUserById($userId);
        if (!$user['allow_sensor_community']) {
            foreach($data as $i => $d) {
                if ($d['update_mode'] == 'pull') {
                    unset($data[$i]);
                }
            }
        }

        return $data;
    }

    public function getAllUserDevices($userId) {
        $stmt = $this->pdo->prepare("
        (SELECT `d1`.* FROM `devices` `d1`
            WHERE `d1`.`user_id` = ?)
        UNION DISTINCT
        (SELECT `d2`.* FROM `device_hierarchy` `dh`
            LEFT JOIN `devices` `d2` ON `d2`.`id` = `dh`.`device_id`
            WHERE `dh`.`user_id` = ?)
        ORDER BY `default_device` DESC, `id` ASC");
        $stmt->execute([$userId, $userId]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $user = $this->userModel->getUserById($userId);
        if (!$user['allow_sensor_community']) {
            foreach($data as $i => $d) {
                if ($d['update_mode'] == 'pull') {
                    unset($data[$i]);
                }
            }
        }

        return $data;
    }

    public function deleteDevice($deviceId) {
        $stmt = $this->pdo->prepare("DELETE FROM `devices` WHERE `id` = ?");
        $stmt->execute([$deviceId]);
        $stmt->closeCursor();
    }

    public function addAdjustment($deviceId, $dbName, $multiplier, $offset) {
        $stmt = $this->pdo->prepare("INSERT INTO `device_adjustments` (`device_id`, `db_name`, `multiplier`, `offset`) VALUES (?, ?, ?, ?)");
        $stmt->execute([$deviceId, $dbName, $multiplier, $offset]);
        $stmt->closeCursor();
    }

    public function deleteAdjustment($deviceId, $adjustmentId) {
        $stmt = $this->pdo->prepare("DELETE FROM `device_adjustments` WHERE `device_id` = ? AND `id` = ?");
        $stmt->execute([$deviceId, $adjustmentId]);
        $stmt->closeCursor();
    }

    public function getDeviceAdjustments($deviceId) {
        $stmt = $this->pdo->prepare("SELECT `id`, `db_name`, `multiplier`, `offset` FROM `device_adjustments` WHERE `device_id` = ?");
        $stmt->execute([$deviceId]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    public function addMapping($deviceId, $dbName, $jsonName) {
        $stmt = $this->pdo->prepare("INSERT INTO `device_mapping` (`device_id`, `db_name`, `json_name`) VALUES (?, ?, ?)");
        $stmt->execute([$deviceId, $dbName, $jsonName]);
        $stmt->closeCursor();
    }

    public function deleteMapping($deviceId, $mappingId) {
        $stmt = $this->pdo->prepare("DELETE FROM `device_mapping` WHERE `device_id` = ? AND `id` = ?");
        $stmt->execute([$deviceId, $mappingId]);
        $stmt->closeCursor();
    }

    public function getMappingForDevice($deviceId) {
        $stmt = $this->pdo->prepare("SELECT `id`, `db_name`, `json_name` FROM `device_mapping` WHERE `device_id` = ?");
        $stmt->execute([$deviceId]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    public function getSensors($type = 'sensor.community') {
        $stmt = $this->pdo->prepare("SELECT * FROM `device_sensors` WHERE `type` = ?");
        $stmt->execute([$type]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    public function getSensorIds($deviceId) {
        $stmt = $this->pdo->prepare("SELECT `sensor_id`, `type` FROM `device_sensors` WHERE `device_id` = ?");
        $stmt->execute([$deviceId]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    public function insertSensor($deviceId, $sensorId, $type = 'sensor.community') {
        $stmt = $this->pdo->prepare("INSERT INTO `device_sensors` (`device_id`, `sensor_id`, `type`) VALUES (?, ?, ?)");
        $stmt->execute([$deviceId, $sensorId, $type]);
        $stmt->closeCursor();
    }

    public function deleteSensorId($deviceId, $sensorId) {
        $stmt = $this->pdo->prepare("DELETE FROM `device_sensors` WHERE `device_id` = ? AND `sensor_id` = ?");
        $stmt->execute([$deviceId, $sensorId]);
        $stmt->closeCursor();
    }

    public function getMappingAsAMap($deviceId) {
        $mapping = array();
        foreach ($this->getMappingForDevice($deviceId) as $m) {
            if (!isset($mapping[$m['db_name']])) {
                $mapping[$m['db_name']] = array();
            }
            $mapping[$m['db_name']][] = $m['json_name'];
        }
        return $mapping;
    }

    public function updateDevice($deviceId, $data) {
        $sql = "UPDATE `devices` SET ";
        foreach ($data as $k => $v) {
            $sql .= "`$k` = ?, ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE `id` = ?";

        $stmt = $this->pdo->prepare($sql);
        $params = array_values($data);
        $params[] = $deviceId;
        $stmt->execute($params);
        $stmt->closeCursor();
    }

    public function makeDefault($userId, $deviceId) {
        $stmt = $this->pdo->prepare("UPDATE `devices` SET `default_device` = IF (`id` = ?, 1, 0) WHERE `user_id` = ?");
        $stmt->execute([$deviceId, $userId]);
        $stmt->closeCursor();
    }
}
?>