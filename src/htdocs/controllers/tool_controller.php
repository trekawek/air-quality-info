<?php
class ToolController extends AbstractController {

    private $dao;

    private $madaviMigrator;

    private $rrdToMysqlMigrator;

    public function __construct($dao, $updater) {
        parent::__construct();
        $this->dao = $dao;
        $this->madaviMigrator = new MadaviMigrator($dao, $updater);
        $this->rrdToMysqlMigrator = new RrdToMysqlMigrator($dao);
    }

    public function index($device) {
        $this->authenticate($device);
        $this->render(array('view' => 'views/tools.php'), array(
            'db_type' => CONFIG['db']['type']
        ));
    }

    public function migrate_madavi($device) {
        $this->authenticate($device);
        header('Content-Type: text/event-stream');
        
        $this->madaviMigrator->migrate($device);
        echo "Madavi records has been imported";
    }

    public function migrate_rrd_to_mysql($device) {
        $this->authenticate($device);
        header('Content-Type: text/event-stream');
        
        $this->rrdToMysqlMigrator->migrate($device);
        echo "RRD database has been migrated to MySQL";
    }

    public function update_rrd_schema($device) {
        $this->authenticate($device);
        header('Content-Type: text/event-stream');

        $this->dao->createDb($device['esp8266id']);
        echo "RRD schema updated";
    }

}
?>