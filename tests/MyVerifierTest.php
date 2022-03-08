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
    public function testSetupShouldRiseExceptionIfNoTokenBeenSet()
    {
        $this->expectException(MyEmailVerifierException::class);

        app(MyEmailVerifier::class)->validate('test@hello.com');;
    }

    public function testValidateShouldReturnTokenIfTokenInConfig()
    {
        config()->set('myemailverifier.token', 'test');

        app(MyEmailVerifier::class)->toArray()['token'];

        $this->assertEquals('test', app(MyEmailVerifier::class)->toArray()['token']);
    }

    public function testValidateShouldHandleSupplierException()
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

    public function testValidateShouldHandleIncorrectJsonFormatException()
    {
        $this->expectException(MyEmailVerifierException::class);

        $this->bindSuccessResponse('Invalid', '{dddddd');

        app(MyEmailVerifier::class)->validate('test@gmail.com');
    }

    public function testValidateShouldRespondWithValidStatus()
    {
        $this->bindSuccessResponse('Valid');

        $this->assertTrue(app(MyEmailVerifier::class)->validate('test@gmail.com')->getStatus()->isValid());
    }

    public function testValidateShouldRespondWithInvalidStatus()
    {
        $this->bindSuccessResponse('Invalid');

        $this->assertTrue(app(MyEmailVerifier::class)->validate('test@gmail.com')->getStatus()->isInvalid());
    }

    public function testValidateShouldRespondWithUnknownStatus()
    {
        $this->bindSuccessResponse('Unknown');

        $this->assertTrue(app(MyEmailVerifier::class)->validate('test@gmail.com')->getStatus()->isUnknown());
    }

    public function testValidateShouldReturnFalseIfEmailIsNotValid()
    {
        $this->bindSuccessResponse('Invalid');

        $this->assertFalse(app(MyEmailVerifier::class)->valid('test@gmail.com'));
    }

    public function testValidateShouldReturnTrueIfEmailIsxValid()
    {
        $this->bindSuccessResponse('Valid');

        $this->assertTrue(app(MyEmailVerifier::class)->valid('test@gmail.com'));
    }

    public function testShouldReturnCorrectResponseIfApiLeftCredits()
    {
        $this->expectException(MyEmailVerifierException::class);

        $this->bindSuccessResponse('Invalid', '{"status":0, "msg":"You do not have enough credits."}');

        app(MyEmailVerifier::class)->valid('test@gmail.com');
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
