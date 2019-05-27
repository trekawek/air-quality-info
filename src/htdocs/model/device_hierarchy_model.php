<?php
namespace AirQualityInfo\Model;

class DeviceHierarchyModel {

    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    private function createRoot($userId) {
        $insertStmt = $this->mysqli->prepare("INSERT INTO `device_hierarchy` (`user_id`, `position`) VALUES (?, 0)");
        $insertStmt->bind_param('i', $userId);
        $insertStmt->execute();
        $insertStmt->close();
        return $this->mysqli->insert_id;
    }

    public function getRootId($userId) {
        $stmt = $this->mysqli->prepare("SELECT `id` FROM `device_hierarchy` WHERE `user_id` = ? AND `parent_id` IS NULL");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rootId = null;
        if ($row = $result->fetch_row()) {
            $rootId = $row[0];
        } else {
            $rootId = $this->createRoot($userId);
        }
        $stmt->close();
        return $rootId;
    }

    public function getNode($userId, $id) {
        $stmt = $this->mysqli->prepare("SELECT `id`, `user_id`, `parent_id`, `name`, `description`, `device_id`, `position` FROM `device_hierarchy` WHERE `user_id` = ? AND `id` = ?");
        $stmt->bind_param('ii', $userId, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $node = null;
        if ($row = $result->fetch_assoc()) {
            $node = $row;
        }
        $stmt->close();
        return $node;
    }

    public function addChild($userId, $parentId, $name, $description, $deviceId = null) {
        $this->validateOwnership($userId, $parentId);
        $position = $this->getMaxPosition($userId, $parentId) + 1;
        $insertStmt = $this->mysqli->prepare("INSERT INTO `device_hierarchy` (`user_id`, `parent_id`, `position`, `name`, `description`, `device_id`) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param('iiissi', $userId, $parentId, $position, $name, $description, $deviceId);
        $insertStmt->execute();
        $insertStmt->close();
        return $this->mysqli->insert_id;
    }

    public function updateNode($userId, $nodeId, $name, $description, $deviceId) {
        $updateStmt = $this->mysqli->prepare('UPDATE `device_hierarchy` SET `name` = ?, `description` = ?, `device_id` = ? WHERE `user_id` = ? AND `id` = ?');
        $updateStmt->bind_param('ssiii', $name, $description, $deviceId, $userId, $nodeId);
        $updateStmt->execute();
        $updateStmt->close();
    }

    public function move($userId, $id, $direction) {
        $node = $this->getNode($userId, $id);
        $this->reorderDevices($node['parent_id']); // make sure the positions are consistent

        if ($direction == 'up' && $node['position'] > 0) {
            $updateStmt = $this->mysqli->prepare('UPDATE `device_hierarchy` SET `position` = `position` + 1 WHERE `position` = ? - 1 AND `parent_id` = ?');
            $updateStmt->bind_param('ii', $node['position'], $node['parent_id']);
            $updateStmt->execute();
            $updateStmt->close();

            $updateStmt = $this->mysqli->prepare('UPDATE `device_hierarchy` SET `position` = `position` - 1 WHERE `id` = ?');
            $updateStmt->bind_param('i', $node['id']);
            $updateStmt->execute();
            $updateStmt->close();
        } else if ($direction == 'down' && $node['position'] < $this->getMaxPosition($userId, $node['parent_id'])) {
            $updateStmt = $this->mysqli->prepare('UPDATE `device_hierarchy` SET `position` = `position` - 1 WHERE `position` = ? + 1 AND `parent_id` = ?');
            $updateStmt->bind_param('ii', $node['position'], $node['parent_id']);
            $updateStmt->execute();
            $updateStmt->close();

            $updateStmt = $this->mysqli->prepare('UPDATE `device_hierarchy` SET `position` = `position` + 1 WHERE `id` = ?');
            $updateStmt->bind_param('i', $node['id']);
            $updateStmt->execute();
            $updateStmt->close();
        }
        return $node;
    }

    public function deleteNode($userId, $id) {
        $node = $this->getNode($userId, $id);

        $stmt = $this->mysqli->prepare('DELETE FROM `device_hierarchy` WHERE `user_id` = ? AND `id` = ?');
        $stmt->bind_param('ii', $userId, $id);
        $stmt->execute();
        $stmt->close();

        $this->reorderDevices($node['parent_id']);
    }

    private function reorderDevices($parentId) {
        $stmt = $this->mysqli->prepare("SELECT `id` FROM `device_hierarchy` WHERE `parent_id` = ? ORDER BY `position`");
        $stmt->bind_param('i', $parentId);
        $stmt->execute();
        $result = $stmt->get_result();

        $position = 0;
        $updateStmt = $this->mysqli->prepare('UPDATE `device_hierarchy` SET `position` = ? WHERE `id` = ?');
        while ($row = $result->fetch_row()) {
            $updateStmt->bind_param('ii', $position, $row[0]);
            $updateStmt->execute();
            $position++;
        }
        $updateStmt->close();

        $stmt->close();
    }

    public function getDeviceNodes($userId, $deviceId) {
        $stmt = $this->mysqli->prepare("SELECT id FROM device_hierarchy WHERE user_id = ? AND device_id = ?");
        $stmt->bind_param('ii', $userId, $deviceId);
        $stmt->execute();
        $result = $stmt->get_result();
        $nodes = array();
        while ($r = $result->fetch_row()) {
            $nodes[] = $r[0];
        }
        $stmt->close();
        return $nodes;
    }

    public function getDirectChildren($userId, $parentId) {
        $stmt = $this->mysqli->prepare("
        SELECT `dh`.`id`,
            `dh`.`user_id`,
            `dh`.`parent_id`,
            `dh`.`device_id`,
            `dh`.`position`,
            IFNULL(`d`.`name`, `dh`.`name`) AS `name`,
            IFNULL(`d`.`description`, `dh`.`description`) AS `description`
        FROM `device_hierarchy` `dh`
        LEFT JOIN `devices` `d` ON `d`.`id` = `dh`.`device_id`
        WHERE `dh`.`user_id` = ? AND `parent_id` = ? ORDER BY `position`");
        $stmt->bind_param('ii', $userId, $parentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $children = array();
        while ($r = $result->fetch_assoc()) {
            $children[] = $r;
        }
        $stmt->close();
        return $children;
    }

    public function getAllNodes($userId) {
        $stmt = $this->mysqli->prepare("
        SELECT `dh`.`id`,
            `dh`.`user_id`,
            `dh`.`parent_id`,
            `dh`.`device_id`,
            `dh`.`position`,
            IFNULL(`d`.`name`, `dh`.`name`) AS `name`,
            IFNULL(`d`.`description`, `dh`.`description`) AS `description`
        FROM `device_hierarchy` `dh`
        LEFT JOIN `devices` `d` ON `d`.`id` = `dh`.`device_id`
        WHERE `dh`.`user_id` = ? ORDER BY `position`");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $nodes = array();
        while ($r = $result->fetch_assoc()) {
            $nodes[] = $r;
        }
        $stmt->close();
        return $nodes;
    }

    public function getTree($userId, $rootId = null) {
        if ($rootId === null) {
            $rootId = $this->getRootId($userId);
        }
        $nodeById = array();
        $nodeByParentId = array();
        foreach ($this->getAllNodes($userId) as $node) {
            $nodeById[$node['id']] = $node;
            $parentId = $node['parent_id'];
            if (!isset($nodeByParentId[$parentId])) {
                $nodeByParentId[$parentId] = array();
            }
            $nodeByParentId[$parentId][] = $node;
        }
        $root = $nodeById[$rootId];
        DeviceHierarchyModel::addChildren($root, $nodeByParentId);
        return $root;
    }

    private static function addChildren(&$node, &$nodeByParentId) {
        $nodeId = $node['id'];
        if (!isset($nodeByParentId[$nodeId])) {
            return;
        }
        $node['children'] = $nodeByParentId[$node['id']];
        foreach ($node['children'] as $i => $c) {
            DeviceHierarchyModel::addChildren($c, $nodeByParentId);
            $node['children'][$i] = $c;
        }
    }

    public function getDevicePaths($userId, $deviceId) {
        $paths = array();
        foreach ($this->getDeviceNodes($userId, $deviceId) as $nodeId) {
            $path = $this->getPath($userId, $nodeId);
            $paths[] = DeviceHierarchyModel::getTextPath($path);
        }
        return $paths;
    }

    public function getPath($userId, $nodeId) {
        $nodeById = array();
        $nodeByParentId = array();
        foreach ($this->getAllNodes($userId) as $node) {
            $nodeById[$node['id']] = $node;
            $parentId = $node['parent_id'];
            if (!isset($nodeByParentId[$parentId])) {
                $nodeByParentId[$parentId] = array();
            }
            $nodeByParentId[$parentId][] = $node;
        }
        $node = $nodeById[$nodeId];
        $nodes = array();
        while ($node['parent_id'] !== null) {
            $nodes[] = $node;
            $node = $nodeById[$node['parent_id']];
        }
        $nodes[] = $node;
        return array_reverse($nodes);
    }

    public static function getTextPath($path) {
        $link = '';
        foreach($path as $n) {
            if (!empty($n['name'])) {
                $link .= '/'.$n['name'];
            }
        }
        return $link;
    }

    private function validateOwnership($userId, $nodeId) {
        $stmt = $this->mysqli->prepare("SELECT `user_id` FROM `device_hierarchy` WHERE `id` = ?");
        $stmt->bind_param('i', $nodeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $valid = false;
        if ($row = $result->fetch_row()) {
            if ($row[0] === $userId) {
                $valid = true;
            }
        }
        $stmt->close();
        if (!$valid) {
            throw new \Exception("Device hierarchy node $nodeId doesn't belong to user $userId");
        }
        return true;
    }

    private function getMaxPosition($userId, $parentId) {
        $stmt = $this->mysqli->prepare("SELECT MAX(`position`) FROM `device_hierarchy` WHERE `user_id` = ? AND `parent_id` = ?");
        $stmt->bind_param('ii', $userId, $parentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $pos = null;
        if ($row = $result->fetch_row()) {
            $pos = $row[0];
        }
        if ($pos === null) {
            $pos = -1;
        }
        $stmt->close();
        return $pos;
    }
}
?>