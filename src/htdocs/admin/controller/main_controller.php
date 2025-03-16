<?php
namespace AirQualityInfo\Admin\Controller;

class MainController extends AbstractController {

    private $currentLocale;

    public function __construct($currentLocale) {
        $this->authorizationRequired = false;
        $this->currentLocale = $currentLocale;
    }

    public function index() {
        $this->static('index');
    }

    public function news() {
        $this->static('news');
    }

    public function robots() {
        header('Content-type: text/plain; charset=utf-8');
        echo file_get_contents('robots.txt');
    }

    public function static($pageName) {
        $path = $this->findPath($pageName);
        if ($path === null) {
            http_response_code(404);
            die();
        }

        $args = array(
            'head' => array('admin/partials/about/head.php'),
            'tail' => array()
        );

        if (substr($path, -3) === '.md') {
            $args['head'][] = 'admin/views/static/md-head.php';
            $args['body'] = \Parsedown::instance()->text(file_get_contents($path));
            $args['tail'][] = 'admin/views/static/md-tail.php';
        } else {
            $args['view'] = $path;
        }

        $args['tail'][] = 'admin/partials/about/tail.php';
        
        $this->render($args);
    }

    private function findPath($pageName) {
        $pageName = preg_replace('/[^a-z-]/', '', $pageName);
        foreach (array($this->currentLocale->getCurrentLang(), 'en') as $lang) {
            foreach (array('php', 'md') as $ext) {
                $path = "admin/views/static/$pageName-$lang.$ext";
                if (file_exists($path)) {
                    return $path;
                }
            }
        }
        return null;
    }
}
?>