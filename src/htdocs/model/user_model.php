<?php
namespace AirQualityInfo\Model;

class UserModel {

    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function parseFqdn($fqdn) {
        $userId = null;
        $isStandardDomain = false;
        foreach (CONFIG['user_domain_suffixes'] as $suffix) {
            if (substr($fqdn, -strlen($suffix)) === $suffix) {
                $isStandardDomain = true;
                $subdomain = substr($fqdn, 0, -strlen($suffix));
                $userId = $this->getIdByDomain($subdomain);
            }
        }
        if (!$isStandardDomain) {
            $userId = $this->getIdByCustomFqdn($fqdn);
        }
        return $userId;
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

    public function getIdByCustomFqdn($fqdn) {
        $stmt = $this->mysqli->prepare("SELECT `user_id` FROM `custom_domains` WHERE `fqdn` = ?");
        $stmt->bind_param('s', $fqdn);
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

    public function updatePassword($userId, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insertStmt = $this->mysqli->prepare("UPDATE `users` SET `password_hash` = ? WHERE `id` = ?");
        $insertStmt->bind_param('si', $hash, $userId);
        $insertStmt->execute();
        $insertStmt->close();
    }

    public function updateUser($userId, $data) {
        $sql = "UPDATE `users` SET ";
        foreach ($data as $k => $v) {
            $sql .= "`$k` = ?, ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE `id` = ?";

        $stmt = $this->mysqli->prepare($sql);
        $params = array_values($data);
        $params[] = $userId;
        $stmt->bind_param(str_repeat('s', count($data)).'i', ...$params);
        $stmt->execute();
    }
}
?>