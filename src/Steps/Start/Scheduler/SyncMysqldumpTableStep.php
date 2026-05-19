<?php

namespace Codinglabs\YoloAlpha\Steps\Start\Scheduler;

use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Concerns\ResolvesDatabases;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsScheduler;

class SyncMysqldumpTableStep implements RunsOnAwsScheduler
{
    use ResolvesDatabases;

    public function __invoke(array $options): StepResult
    {
        $dir = Paths::yoloDir();
        $file = sprintf('%s/mysqldump-table.sh', $dir);

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
                subject: file_get_contents(Paths::stubs('mysqldump-table.sh.stub'))
            )
        );

        return StepResult::SYNCED;
    }
}
