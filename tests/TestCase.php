<?php

namespace Sirvantos\MyEmailVerifier\Tests;

use Sirvantos\MyEmailVerifier\MyEmailVerifierServiceProvider;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\Concerns\InteractsWithContainer;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\View;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use InteractsWithContainer;

    protected $testNow = true;

    public function setUp(): void
    {
        parent::setUp();

        if ($this->testNow) {
            $this->setNow(2019, 1, 1);
        };
    }

    protected function getPackageProviders($app)
    {
        return [MyEmailVerifierServiceProvider::class];
    }

    protected function setNow($year, int $month = 1, int $day = 1)
    {
        $newNow = $year instanceof CarbonInterface
            ? $year->copy()
            : Date::createFromDate($year, $month, $day);

        $newNow = $newNow->startOfDay();

        Date::setTestNow($newNow);
    }
}
