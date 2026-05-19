<?php

namespace Codinglabs\YoloAlpha\Steps\Network;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Commands\Command;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Exceptions\IntegrityCheckException;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

use function Laravel\Prompts\note;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\warning;

class SyncKeyPairStep implements Step
{
    public function __invoke(array $options, Command $command): StepResult
    {
        try {
            AwsResources::keyPair();

            return StepResult::SYNCED;
        } catch (ResourceDoesNotExistException $e) {
            if (! Arr::get($options, 'dry-run')) {
                if (Manifest::get('aws.ec2.key-pair')) {
                    throw IntegrityCheckException::make('yolo.yml specifies a custom EC2 key pair which does not exist');
                }

                $name = Manifest::get('aws.ec2.key-pair', Helpers::keyedResourceName(exclusive: false));

                $key = Aws::ec2()->createKeyPair([
                    'KeyName' => $name,
                    'TagSpecifications' => [
                        [
                            'ResourceType' => 'key-pair',
                            ...Aws::tags([
                                'Name' => $name,
                            ]),
                        ],
                    ],
                ]);

                $envFilename = '.env';
                $suggestedPath = sprintf('~/.ssh/%s', $name);
                $suggestedEnv = sprintf('%s=%s', Helpers::keyedEnvName('SSH_KEY'), $suggestedPath);

                $command->after(function () use ($suggestedPath, $key) {
                    intro(
                        sprintf(
                            'A key pair has been created to access EC2 instances. Save the below private key to somewhere like %s',
                            $suggestedPath
                        )
                    );

                    note($key['KeyMaterial']);
                });

                if (file_exists(Paths::base($envFilename))) {
                    file_put_contents(
                        Paths::base($envFilename),
                        PHP_EOL . $suggestedEnv . PHP_EOL,
                        FILE_APPEND
                    );

                    $command->after(fn () => warning("$suggestedEnv has been added to $envFilename. Update as required to match the location where you saved the private key."));
                } else {
                    $command->after(fn () => warning(sprintf("Could not find $envFilename in the current directory. You will need to add an entry like %s to allow YOLO to authenticate.", $suggestedEnv)));
                }

                $command->after(fn () => warning(sprintf("Re-run 'yolo network:sync %s' after saving the private key to complete setup.", Helpers::environment())));

                return StepResult::CREATED;
            }

            return Manifest::get('aws.ec2.key-pair')
                ? StepResult::MANIFEST_INVALID
                : StepResult::WOULD_CREATE;
        }
    }
}
