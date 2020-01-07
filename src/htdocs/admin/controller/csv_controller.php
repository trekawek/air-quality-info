<?php
namespace AirQualityInfo\Admin\Controller;

class CsvController extends AbstractController {

    private $csvModel;

    public function __construct(
            \AirQualityInfo\Model\CsvModel $csvModel) {
        $this->csvModel = $csvModel;
        $this->title = __('CSV');
    }

    public function index($path = null) {
        if (empty($path)) {
            $path = $this->user['domain'];
        }
        $this->render(array(
            'view' => 'admin/views/csv/index.php'
        ), array(
            'path' => $path,
            'list' => $this->csvModel->list($this->sanitizePath($path))
        ));
    }

    public function downloadFile($path = null) {
        if (!$this->csvModel->downloadFile($this->sanitizePath($path))) {
            http_response_code(404);
            die();
        }
    }

    public function downloadDir($path = null) {
        if (!$this->csvModel->downloadDir($this->sanitizePath($path))) {
            http_response_code(404);
            die();
        }
    }

    private function sanitizePath($path) {
        $result = array();
        $path = explode('/', $path);
        $prefix = array();
        foreach($path as $segment) {
            if (!empty($segment)) {
                $prefix[]= $segment;
            }
        }
        if ($prefix[0] !== $this->user['domain']) {
            http_response_code(404);
            die();
        }
        return implode('/', $prefix);
    }

}
?>