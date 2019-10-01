<?php
namespace AirQualityInfo\Model;

class AttachmentModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function saveUploadedFile($userId, $name, $file) {
        $this->deleteFile($userId, $name);
        $stmt = $this->pdo->prepare("INSERT INTO `attachments` (`user_id`, `name`, `filename`, `length`, `mime`, `data`) VALUES (:userId, :name, :filename, :length, :mime, :data)");
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':filename', $file['name']);
        $stmt->bindParam(':length', $file['size']);
        $stmt->bindParam(':mime', $file['type']);
        $data = file_get_contents($file['tmp_name']);
        $stmt->bindParam(':data', $data, \PDO::PARAM_LOB);
        $stmt->execute();
        $stmt->closeCursor();
        unlink($file['tmp_name']);
    }

    public function deleteFile($userId, $name) {
        $stmt = $this->pdo->prepare("DELETE FROM `attachments` WHERE `user_id` = ? AND `name` = ?");
        $stmt->execute([$userId, $name]);
        $stmt->closeCursor();
    }

    public function getFileInfo($userId, $name) {
        $stmt = $this->pdo->prepare("SELECT `filename`, `length`, `mime` FROM `attachments` WHERE `user_id` = ? AND `name` = ?");
        $stmt->execute([$userId, $name]);
        $file = null;
        if ($row = $stmt->fetch()) {
            $file = $row;
        }
        $stmt->closeCursor();
        return $file;
    }

    public function getFileData($userId, $name) {
        $stmt = $this->pdo->prepare("SELECT `data` FROM `attachments` WHERE `user_id` = ? AND `name` = ?");
        $stmt->execute([$userId, $name]);
        $data = null;
        if ($row = $stmt->fetch()) {
            $data = $row['data'];
        }
        $stmt->closeCursor();
        return $data;
    }
}
?>