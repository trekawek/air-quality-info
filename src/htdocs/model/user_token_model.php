<?php
namespace AirQualityInfo\Model;

class UserTokenModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function generateToken($userId) {
        $token = bin2hex(random_bytes(16));
        $stmt = $this->pdo->prepare("INSERT INTO `user_tokens` (`user_id`, `token`, `valid_until`) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $token, time() + 15 * 60]);
        $stmt->closeCursor();
        return $token;
    }

    public function getUserIdByToken($token) {
        $stmt = $this->pdo->prepare("SELECT `user_id` FROM `user_tokens` WHERE `token` = ? AND valid_until >= UNIX_TIMESTAMP()");
        $stmt->execute([$token]);
        $user = null;
        if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $user = $row;
        }
        $stmt->closeCursor();
        return $user['user_id'];
    }

    public function deleteToken($token) {
        $stmt = $this->pdo->prepare("DELETE FROM `user_tokens` WHERE `token` = ?");
        $stmt->execute([$token]);
        $stmt->closeCursor();
    }
}
?>