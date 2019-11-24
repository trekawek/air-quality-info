<?php
namespace AirQualityInfo\Model;

class WidgetModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createWidget($userId, $title) {
        $stmt = $this->pdo->prepare("INSERT INTO `widgets` (`user_id`, `title`) VALUES (?, ?)");
        $stmt->execute([$userId, $title]);
        $stmt->closeCursor();
        return $this->pdo->lastInsertId();
    }

    public function updateWidget($userId, $widgetId, $title) {
        $stmt = $this->pdo->prepare("UPDATE `widgets` SET `title` = ? WHERE `user_id` = ? AND `id` = ?");
        $stmt->execute([$title, $userId, $widgetId]);
        $stmt->closeCursor();
    }

    public function getWidgetsForUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM `widgets` WHERE `user_id` = ?");
        $stmt->execute([$userId]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    public function getWidgetById($userId, $widgetId) {
        $stmt = $this->pdo->prepare("SELECT * FROM `widgets` WHERE `user_id` = ? AND `id` = ?");
        $stmt->execute([$userId, $widgetId]);
        $widget = null;
        if ($row = $stmt->fetch()) {
            $widget = $row;
        }
        $stmt->closeCursor();
        return $widget;
    }

    public function deleteWidget($userId, $widgetId) {
        $stmt = $this->pdo->prepare("DELETE FROM `widgets` WHERE `user_id` = ? AND `id` = ?");
        $stmt->execute([$userId, $widgetId]);
        $stmt->closeCursor();
    }
}
?>