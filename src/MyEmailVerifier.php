<?php

namespace Sirvantos\MyEmailVerifier;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Sirvantos\MyEmailVerifier\Services\Suppliers\Contracts\CanSupply;
use Sirvantos\MyEmailVerifier\Services\Suppliers\Exceptions\SupplierException;
use Sirvantos\MyEmailVerifier\Exceptions\MyEmailVerifierException;
use Sirvantos\MyEmailVerifier\Responses\MyEmailVerifierResponse;

class MyEmailVerifier implements Arrayable
{
    private const URL = 'https://client.myemailverifier.com/verifier/validate_single/%s/%s';

    private CanSupply $supplier;

    public function __construct(CanSupply $supplier)
    {
        $this->supplier = $supplier;
    }

    public function validate(string $email): MyEmailVerifierResponse
    {
        try {
            $response = $this->supplier->supply(['method' => Request::METHOD_GET, 'uri' => $this->buildUrl($email)]);
        } catch (SupplierException $e) {
            throw new MyEmailVerifierException("Supplier exception", 0, $e);
        }

        if (!$json = json_decode($response->getBody(), true)) {
            throw new MyEmailVerifierException("Unknown response");
        }

        if (isset($json['status']) && $json['status'] === 0) {
            throw new MyEmailVerifierException( $json['msg'] ?? "Api error");
        }

        return MyEmailVerifierResponse::make($this->sanitize($json));
    }

    public function valid(string $email): bool
    {
        return $this->validate($email)->getStatus()->isValid();
    }

    public function token(): string
    {
        throw_if(
            !$token = config('myemailverifier.token'), new MyEmailVerifierException('Knows nothing about token')
        );

        return $token;
    }

    public function toArray(): array
    {
        return [
            'token' => $this->token()
        ];
    }

    private function buildUrl(string $email): string
    {
        return sprintf(self::URL, $email, $this->token());
    }

    private function sanitize(array $payload): array
    {
        return
            collect($payload)
                ->mapWithKeys(fn($item, $key) => $key === 'StatusCode' ? ['status_code' => $item] : [$key => $item])
                ->mapWithKeys(fn($item, $key) => [Str::lower($key) => $item])
                ->all();
    }
}
