<?php
namespace AirQualityInfo\Admin\Controller;

class AttachmentController extends AbstractController {

    private $attachmentModel;

    public function __construct(\AirQualityInfo\Model\AttachmentModel $attachmentModel) {
        $this->attachmentModel = $attachmentModel;
    }

    public function get($name) {
        $metadata = $this->attachmentModel->getFileInfo($this->user['id'], $name);
        if ($metadata === null) {
            http_response_code(404);
            die();
        }

        header("Content-Type: ".$metadata['mime']);
        header("Content-Length: ".$metadata['length']);
        if (isset($_GET['save'])) {
            header('Content-Disposition: attachment; filename='.$metadata['filename']);
        }

        echo $this->attachmentModel->getFileData($this->user['id'], $name);
    }
}
?>