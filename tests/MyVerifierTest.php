<?php

namespace Sirvantos\MyEmailVerifier\Tests;

use GuzzleHttp\Psr7\Response;
use Sirvantos\MyEmailVerifier\MyEmailVerifier;
use Sirvantos\MyEmailVerifier\Exceptions\MyEmailVerifierException;
use Sirvantos\MyEmailVerifier\Services\Suppliers\Guzzle;
use Sirvantos\MyEmailVerifier\Services\Suppliers\Contracts\CanSupply;
use Sirvantos\MyEmailVerifier\Services\Suppliers\Exceptions\SupplierException;

class MyVerifierTest extends TestCase
{
    public function testMyEmailVerifierSetupShouldRiseExceptionIfNoTokenBeenSet()
    {
        $this->expectException(MyEmailVerifierException::class);

        app(MyEmailVerifier::class)->validate('test@hello.com');;
    }

    public function testMyEmailVerifierSetupShouldReturnTokenIfTokenInConfig()
    {
        config()->set('myemailverifier.token', 'test');

        app(MyEmailVerifier::class)->toArray()['token'];

        $this->assertEquals('test', app(MyEmailVerifier::class)->toArray()['token']);
    }

    public function testMyEmailVerifierShouldHandleSupplierException()
    {
        $this->expectException(MyEmailVerifierException::class);

        config()->set('myemailverifier.token', 'test');

        $guzzleMock = $this->partialMock(Guzzle::class, function ($mock) {
            $mock
                ->shouldReceive('supply')
                ->andThrows(new SupplierException('Supplier Exception', 0));

        });

        $this->app->bind(CanSupply::class, fn($app) => $guzzleMock);

        app(MyEmailVerifier::class)->validate('test@gmail.com');
    }

    public function testMyEmailVerifierShouldHandleIncorrectJsonFormatException()
    {
        $this->expectException(MyEmailVerifierException::class);

        $this->bindSuccessResponse('Invalid', '{dddddd');

        app(MyEmailVerifier::class)->validate('test@gmail.com');
    }

    public function testMyEmailVerifierShouldRespondWithValidStatus()
    {
        $this->bindSuccessResponse('Valid');

        $this->assertTrue(app(MyEmailVerifier::class)->validate('test@gmail.com')->getStatus()->isValid());
    }

    public function testMyEmailVerifierShouldRespondWithInvalidStatus()
    {
        $this->bindSuccessResponse('Invalid');

        $this->assertTrue(app(MyEmailVerifier::class)->validate('test@gmail.com')->getStatus()->isInvalid());
    }

    public function testMyEmailVerifierShouldRespondWithUnknownStatus()
    {
        $this->bindSuccessResponse('Unknown');

        $this->assertTrue(app(MyEmailVerifier::class)->validate('test@gmail.com')->getStatus()->isUnknown());
    }

    private function bindSuccessResponse(string $verificationStatus, string $body = '')
    {
        config()->set('myemailverifier.token', 'test');

        $guzzleMock = $this->partialMock(Guzzle::class, function ($mock) use ($body, $verificationStatus) {
            $mock
                ->shouldReceive('supply')
                ->andReturn(
                    new Response(
                        200,
                        [],
                        $body ?:'{"Address":"support@myemailverifier.com", "StatusCode":"2.1.5", "Status":"'
                        . $verificationStatus . '", "Disposable_Domain":"false", '
                        . '"Free_Domain":"false", "Greylisted":"false", "Diagnosis":"Mailbox Exists and Active"}'
                    )
                );
        });

        $this->app->bind(CanSupply::class, fn($app) => $guzzleMock);
    }
}
