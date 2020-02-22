<?php
namespace AirQualityInfo\Model;

class DeviceHierarchyModel {

    private $pdo;

    private $userModel;

    public function __construct($pdo, UserModel $userModel) {
        $this->pdo = $pdo;
        $this->userModel = $userModel;
    }

    private function createRoot($userId) {
        $stmt = $this->pdo->prepare("INSERT INTO `device_hierarchy` (`user_id`, `position`) VALUES (?, 0)");
        $stmt->execute([$userId]);
        return $this->pdo->lastInsertId();
    }

    public function getRootId($userId) {
        $stmt = $this->pdo->prepare("SELECT `id` FROM `device_hierarchy` WHERE `user_id` = ? AND `parent_id` IS NULL");
        $stmt->execute([$userId]);
        $rootId = null;
        if ($row = $stmt->fetch()) {
            $rootId = $row['id'];
        } else {
            $rootId = $this->createRoot($userId);
        }
        $stmt->closeCursor();
        return $rootId;
    }

    public function getNode($userId, $id) {
        $stmt = $this->pdo->prepare("SELECT `id`, `user_id`, `parent_id`, `name`, `description`, `device_id`, `position` FROM `device_hierarchy` WHERE `user_id` = ? AND `id` = ?");
        $stmt->execute([$userId, $id]);
        $node = $stmt->fetch();
        $stmt->closeCursor();
        return $node;
    }

    public function addChild($userId, $parentId, $name, $description, $deviceId = null) {
        $this->validateOwnership($userId, $parentId);
        $position = $this->getMaxPosition($userId, $parentId) + 1;
        $insertStmt = $this->pdo->prepare("INSERT INTO `device_hierarchy` (`user_id`, `parent_id`, `position`, `name`, `description`, `device_id`) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStmt->execute([$userId, $parentId, $position, $name, $description, $deviceId]);
        $insertStmt->closeCursor();
        return $this->pdo->lastInsertId();
    }

    public function updateNode($userId, $nodeId, $name, $description, $deviceId) {
        $updateStmt = $this->pdo->prepare('UPDATE `device_hierarchy` SET `name` = ?, `description` = ?, `device_id` = ? WHERE `user_id` = ? AND `id` = ?');
        $updateStmt->execute([$name, $description, $deviceId, $userId, $nodeId]);
        $updateStmt->closeCursor();
    }

    public function move($userId, $id, $direction) {
        $node = $this->getNode($userId, $id);
        $this->reorderDevices($node['parent_id']); // make sure the positions are consistent

        if ($direction == 'up' && $node['position'] > 0) {
            $updateStmt = $this->pdo->prepare('UPDATE `device_hierarchy` SET `position` = `position` + 1 WHERE `position` = ? - 1 AND `parent_id` = ?');
            $updateStmt->execute([$node['position'], $node['parent_id']]);
            $updateStmt->closeCursor();

            $updateStmt = $this->pdo->prepare('UPDATE `device_hierarchy` SET `position` = `position` - 1 WHERE `id` = ?');
            $updateStmt->execute([$node['id']]);
            $updateStmt->closeCursor();
        } else if ($direction == 'down' && $node['position'] < $this->getMaxPosition($userId, $node['parent_id'])) {
            $updateStmt = $this->pdo->prepare('UPDATE `device_hierarchy` SET `position` = `position` - 1 WHERE `position` = ? + 1 AND `parent_id` = ?');
            $updateStmt->execute([$node['position'], $node['parent_id']]);
            $updateStmt->closeCursor();

            $updateStmt = $this->pdo->prepare('UPDATE `device_hierarchy` SET `position` = `position` + 1 WHERE `id` = ?');
            $updateStmt->execute([$node['id']]);
            $updateStmt->closeCursor();
        }
        return $node;
    }

    public function deleteNode($userId, $id) {
        $node = $this->getNode($userId, $id);

        $stmt = $this->pdo->prepare('DELETE FROM `device_hierarchy` WHERE `user_id` = ? AND `id` = ?');
        $stmt->execute([$userId, $id]);
        $stmt->closeCursor();

        $this->reorderDevices($node['parent_id']);
    }

    private function reorderDevices($parentId) {
        $stmt = $this->pdo->prepare("SELECT `id` FROM `device_hierarchy` WHERE `parent_id` = ? ORDER BY `position`");
        $stmt->execute([$parentId]);

        $position = 0;
        $updateStmt = $this->pdo->prepare('UPDATE `device_hierarchy` SET `position` = ? WHERE `id` = ?');
        while ($row = $stmt->fetch()) {
            $updateStmt->execute([$position, $row['id']]);
            $position++;
        }
        $updateStmt->closeCursor();
        $stmt->closeCursor();
    }

    public function getDeviceNodes($userId, $deviceId) {
        $stmt = $this->pdo->prepare("SELECT id FROM device_hierarchy WHERE user_id = ? AND device_id = ?");
        $stmt->execute([$userId, $deviceId]);
        $nodes = array();
        while ($r = $stmt->fetch()) {
            $nodes[] = $r['id'];
        }
        $stmt->closeCursor();
        return $nodes;
    }

    public function getDirectChildren($userId, $parentId) {
        $stmt = $this->pdo->prepare("
        SELECT `dh`.`id`,
            `dh`.`user_id`,
            `dh`.`parent_id`,
            `dh`.`device_id`,
            `dh`.`position`,
            IFNULL(`d`.`name`, `dh`.`name`) AS `name`,
            IFNULL(`d`.`description`, `dh`.`description`) AS `description`,
            `d`.`user_id` AS `device_user_id`,
            `d`.`location_provided`
        FROM `device_hierarchy` `dh`
        LEFT JOIN `devices` `d` ON `d`.`id` = `dh`.`device_id`
        WHERE `dh`.`user_id` = ? AND `parent_id` = ? ORDER BY `position`");
        $stmt->execute([$userId, $parentId]);
        $children = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $children;
    }

    public function getAllNodes($userId) {
        $stmt = $this->pdo->prepare("
        SELECT `dh`.`id`,
            `dh`.`user_id`,
            `dh`.`parent_id`,
            `dh`.`device_id`,
            `dh`.`position`,
            IFNULL(`d`.`name`, `dh`.`name`) AS `name`,
            IFNULL(`d`.`description`, `dh`.`description`) AS `description`,
            `d`.`location_provided`,
            `d`.`update_mode`
        FROM `device_hierarchy` `dh`
        LEFT JOIN `devices` `d` ON `d`.`id` = `dh`.`device_id`
        WHERE `dh`.`user_id` = ? ORDER BY `position`");
        $stmt->execute([$userId]);
        $nodes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $user = $this->userModel->getUserById($userId);
        if (!$user['allow_sensor_community']) {
            foreach($nodes as $i => $d) {
                if ($d['update_mode'] == 'pull') {
                    unset($nodes[$i]);
                }
            }
        }

        return $nodes;
    }

    public function getAllNodesById($userId) {
        $nodeById = array();
        foreach ($this->getAllNodes($userId) as $n) {
            $nodeById[$n['id']] = $n;
        }
        return $nodeById;
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
        foreach ($this->getAllNodes($userId) as $node) {
            $nodeById[$node['id']] = $node;
        }
        return DeviceHierarchyModel::calculatePath($nodeById, $nodeId);
    }

    public static function calculateDevicePath($nodeById, $deviceId) {
        $nodeId = null;
        foreach ($nodeById as $i => $n) {
            if ($n['device_id'] == $deviceId) {
                $nodeId = $i;
            }
        }
        if ($nodeId === null) {
            return array();
        } else {
            return DeviceHierarchyModel::calculatePath($nodeById, $nodeId);
        }
    }

    public static function calculatePath($nodeById, $nodeId) {
        $node = $nodeById[$nodeId];
        $nodes = array();
        while ($node['parent_id'] !== null) {
            $nodes[] = $node;
            $parentId = $node['parent_id'];
            $node = $nodeById[$parentId];
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
        $stmt = $this->pdo->prepare("SELECT `user_id` FROM `device_hierarchy` WHERE `id` = ?");
        $stmt->execute([$nodeId]);
        $valid = false;
        if ($row = $stmt->fetch()) {
            if ($row['user_id'] === $userId) {
                $valid = true;
            }
        }
        $stmt->closeCursor();
        if (!$valid) {
            throw new \Exception("Device hierarchy node $nodeId doesn't belong to user $userId");
        }
        return true;
    }

    private function getMaxPosition($userId, $parentId) {
        $stmt = $this->pdo->prepare("SELECT MAX(`position`) FROM `device_hierarchy` WHERE `user_id` = ? AND `parent_id` = ?");
        $stmt->execute([$userId, $parentId]);
        $pos = null;
        if ($row = $stmt->fetch()) {
            $pos = $row[0];
        }
        if ($pos === null) {
            $pos = -1;
        }
        $stmt->closeCursor();
        return $pos;
    }

    public static function flatTree($tree, $deviceById) {
        $devices = array();
        if ($tree['device_id']) {
            $devices[] = $deviceById[$tree['device_id']];
        } else if (isset($tree['children'])) {
            foreach ($tree['children'] as $c) {
                $devices = array_merge($devices, DeviceHierarchyModel::flatTree($c, $deviceById));
            }
        }
        return $devices;
    }
}
?>