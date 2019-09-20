<?php
namespace AirQualityInfo\Model;

class UserModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
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
        $stmt = $this->pdo->prepare("SELECT id FROM `users` WHERE `domain` = ?");
        $stmt->execute([$domainName]);
        $id = null;
        if ($row = $stmt->fetch()) {
            $id = $row['id'];
        }
        $stmt->closeCursor();
        return $id;
    }

    public function getIdByCustomFqdn($fqdn) {
        $stmt = $this->pdo->prepare("SELECT `user_id` FROM `custom_domains` WHERE `fqdn` = ?");
        $stmt->execute([$fqdn]);
        $id = null;
        if ($row = $stmt->fetch()) {
            $id = $row['user_id'];
        }
        $stmt->closeCursor();
        return $id;
    }

    public function getUserById($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE `id` = ?");
        $stmt->execute([$userId]);
        $user = null;
        if ($row = $stmt->fetch()) {
            $user = $row;
        }
        $stmt->closeCursor();
        return $user;
    }

    public function getUserByEmail($userEmail) {
        $stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE `email` = ?");
        $stmt->execute([$userEmail]);
        $user = null;
        if ($row = $stmt->fetch()) {
            $user = $row;
        }
        $stmt->closeCursor();
        return $user;
    }

    public function getAllUsers() {
        $stmt = $this->pdo->prepare("SELECT `id`, `domain` FROM `users`");
        $stmt->execute();
        $data = $stmt->fetchAll();
        $stmt->closeCursor();
        return $data;
    }

    public function createUser($email, $password, $domain) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO `users` (`email`, `password_hash`, `domain`) VALUES (?, ?, ?)");
        $stmt->execute([$email, $hash, $domain]);
        $stmt->closeCursor();
        return $this->pdo->lastInsertId();
    }

    public function updatePassword($userId, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE `users` SET `password_hash` = ? WHERE `id` = ?");
        $stmt->execute([$hash, $userId]);
        $stmt->closeCursor();
    }

    public function updateUser($userId, $data) {
        $sql = "UPDATE `users` SET ";
        foreach ($data as $k => $v) {
            $sql .= "`$k` = ?, ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE `id` = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        $stmt->closeCursor();
    }
}
?>