<?php
namespace AirQualityInfo\Model;

class UserModel {

    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function getIdByDomain($domainName) {
        $stmt = $this->mysqli->prepare("SELECT id FROM `users` WHERE `domain` = ?");
        $stmt->bind_param('s', $domainName);
        $stmt->execute();
        $result = $stmt->get_result();
        $id = null;
        if ($row = $result->fetch_row()) {
            $id = $row[0];
        }
        $stmt->close();
        return $id;
    }

    public function getUserById($userId) {
        $stmt = $this->mysqli->prepare("SELECT * FROM `users` WHERE `id` = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = null;
        if ($row = $result->fetch_assoc()) {
            $user = $row;
        }
        $stmt->close();
        return $user;
    }

    public function getUserByEmail($userEmail) {
        $stmt = $this->mysqli->prepare("SELECT * FROM `users` WHERE `email` = ?");
        $stmt->bind_param('s', $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = null;
        if ($row = $result->fetch_assoc()) {
            $user = $row;
        }
        $stmt->close();
        return $user;
    }

    public function createUser($email, $password, $domain) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insertStmt = $this->mysqli->prepare("INSERT INTO `users` (`email`, `password_hash`, `domain`) VALUES (?, ?, ?)");
        $insertStmt->bind_param('sss', $email, $hash, $domain);
        $insertStmt->execute();
        $insertStmt->close();
        return $this->mysqli->insert_id;
    }
}
?>