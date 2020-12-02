#!/usr/bin/env php
<?php
include(getenv('AQI_PATH').'/boot.php');

echo "Starting job-handler\n";

while (true) {
    $job = $beanstalk->reserveWithTimeout(50);
    echo ".\n";
    if ($job !== null) {
        try {
            $data = json_decode($job->getData(), true);

            $jobType = $data['job'];
            $action = $data['action'];
            $args = $data['args'];

            $diContainer->injectClass('\\AirQualityInfo\\Job\\'.\AirQualityInfo\Lib\StringUtils::camelize($jobType).'Job')->$action(...array_values($args));

            echo "Deleting job: {$job->getId()}\n";
            $beanstalk->delete($job);
        } catch (\PDOException $e) {
            echo "Burying job: {$job->getId()}\n";
            $beanstalk->bury($job);
            echo "Restarting handler\n";
            throw $e;
        } catch (\Throwable $t) {
            echo "Exception: {$t->getMessage()}\n";
            echo "Burying job: {$job->getId()}\n";
            $beanstalk->bury($job);
        }
    }
}
?>