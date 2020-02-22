<?php
namespace AirQualityInfo\Model;

class TemplateModel {

    const DISABLED_WITH_SENSOR_COMMUNITY = array('custom_page_name', 'custom_page', 'header', 'footer', 'widget_footer');

    const TYPE_TO_EXT = array(
        'image/png'     => 'png',
        'image/svg+xml' => 'svg'
    );

    const PREFIX_PATH = "public/ugc";

    private $pdo;

    private $userModel;

    public function __construct($pdo, UserModel $userModel) {
        $this->pdo = $pdo;
        $this->userModel = $userModel;
    }

    public function getTemplate($userId) {
        $stmt = $this->pdo->prepare("SELECT `template_name`, `template` FROM `templates` WHERE `user_id` = ?");
        $stmt->execute([$userId]);
        $templates = null;
        while ($row = $stmt->fetch()) {
            $templates[$row['template_name']] = $row['template'];
        }
        $stmt->closeCursor();

        $user = $this->userModel->getUserById($userId);
        if ($user['allow_sensor_community']) {
            foreach (TemplateModel::DISABLED_WITH_SENSOR_COMMUNITY as $name) {
                unset($templates[$name]);
            }
        }
        return $templates;
    }

    public function updateTemplate($userId, $data) {
        $stmt = $this->pdo->prepare("SELECT `template_name` FROM `templates` WHERE `user_id` = ?");
        $stmt->execute([$userId]);
        $oldNames = null;
        while ($row = $stmt->fetch()) {
            $oldNames[] = $row['template_name'];
        }
        $stmt->closeCursor();

        foreach ($data as $k => $v) {
            $v = trim($v);
            if (empty($v)) {
                unset($data[$k]);
            }
        }

        $updateStmt = $this->pdo->prepare("UPDATE `templates` SET `template` = ? WHERE `user_id` = ? AND `template_name` = ?");
        $insertStmt = $this->pdo->prepare("INSERT INTO `templates` (`user_id`, `template_name`, `template`) VALUES (?, ?, ?)");
        $deleteStmt = $this->pdo->prepare("DELETE FROM `templates` WHERE `user_id` = ? AND `template_name` = ?");
        foreach ($data as $templateName => $template) {
            if (in_array($templateName, $oldNames)) {
                $updateStmt->execute([$template, $userId, $templateName]);
            } else {
                $insertStmt->execute([$userId, $templateName, $template]);
            }
        }
        foreach ($oldNames as $templateName) {
            if (!isset($data[$templateName])) {
                $deleteStmt->execute([$userId, $templateName]);
            }
        }
        $deleteStmt->closeCursor();
        $insertStmt->closeCursor();
        $updateStmt->closeCursor();
    }
}
?>