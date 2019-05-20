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

}
?>