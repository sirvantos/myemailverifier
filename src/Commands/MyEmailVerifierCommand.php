<?php

namespace Sirvantos\Myemailverifier\Commands;

use Illuminate\Console\Command;

class MyEmailVerifierCommand extends Command
{
    public $signature = 'myemailverifier';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
