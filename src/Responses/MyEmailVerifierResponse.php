<?php

namespace Sirvantos\MyEmailVerifier\Responses;

use Illuminate\Support\Arr;
use Sirvantos\MyEmailVerifier\Enums\Status;
use Illuminate\Http\Client\Response;

final class MyEmailVerifierResponse extends BaseResponse
{
    private Status $status;
    private string $address;
    private string $statusCode;
    private bool $disposableDomain;
    private bool $freeDomain;
    private bool $greyListed;
    private string $diagnosis;

    public static function make(array $payload): self
    {
        return
            (new self())
                ->setStatus(Status::make(Arr::get($payload, 'status', '')))
                ->setAddress(Arr::get($payload, 'address', ''))
                ->setStatusCode(Arr::get($payload, 'status_code', ''))
                ->setDisposableDomain(Arr::get($payload, 'disposable_domain', ''))
                ->setFreeDomain(Arr::get($payload, 'free_domain', ''))
                ->setDiagnosis(Arr::get($payload, 'diagnosis', ''))
                ->setGreyListed(Arr::get($payload, 'greylisted', ''));
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    public function setStatusCode(string $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function setDisposableDomain(bool $disposableDomain): self
    {
        $this->disposableDomain = $disposableDomain;

        return $this;
    }

    public function setFreeDomain(bool $freeDomain): self
    {
        $this->freeDomain = $freeDomain;

        return $this;
    }

    public function setDiagnosis(string $diagnosis): self
    {
        $this->diagnosis = $diagnosis;

        return $this;
    }

    public function setGreyListed(bool $greyListed): self
    {
        $this->greyListed = $greyListed;

        return $this;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }
}
