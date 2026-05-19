<?php

namespace Codinglabs\YoloAlpha\Steps\Start\Scheduler;

use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Concerns\ResolvesDatabases;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsScheduler;

class SyncMysqlBackupStep implements RunsOnAwsScheduler
{
    use ResolvesDatabases;

    public function __invoke(array $options): StepResult
    {
        $dir = Paths::yoloDir();
        $logDir = Paths::logDir();
        $file = sprintf('%s/mysqlbackup.sh', $dir);
        $cron = sprintf('/etc/cron.d/%s', Helpers::keyedResourceName('mysqlbackup'));

        file_put_contents(
            $file,
            str_replace(
                search: [
                    '{YOLO_DIR}',
                    '{DB_HOST}',
                    '{DB_USERNAME}',
                    '{DB_PASSWORD}',
                    '{AWS_BUCKET}',
                    '{DATABASES}',
                ],
                replace: [
                    $dir,
                    env('DB_REPLICA_HOST', env('DB_HOST')),
                    env('DB_USERNAME'),
                    env('DB_PASSWORD'),
                    Paths::s3ArtefactsBucket(),
                    implode(' ', $this->databases()),
                ],
                subject: file_get_contents(Paths::stubs('mysqlbackup.sh.stub'))
            )
        );

        if (! Manifest::get('mysqldump')) {
            if (file_exists($cron)) {
                unlink($cron);
            }

            return StepResult::SYNCED;
        }

        file_put_contents(
            $cron,
            str_replace(
                search: [
                    '{SCRIPT_PATH}',
                    '{LOG_PATH}',
                ],
                replace: [
                    $file,
                    sprintf('%s/mysqlbackup.log', $logDir),
                ],
                subject: file_get_contents(Paths::stubs('cron/mysqlbackup.stub'))
            )
        );

        return StepResult::SYNCED;
    }
}
