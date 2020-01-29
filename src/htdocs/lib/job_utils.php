<?php
namespace AirQualityInfo\Lib;

class JobUtils {

    private $beanstalk;

    function __construct(\Pheanstalk\Pheanstalk $beanstalk) {
        $this->beanstalk = $beanstalk;
    }

    function createJob($jobType, $action, $args) {
        $job = array(
            'job' => $jobType,
            'action' => $action,
            'args' => $args
        );
        $this->beanstalk->put(json_encode($job));
    }

}
?>