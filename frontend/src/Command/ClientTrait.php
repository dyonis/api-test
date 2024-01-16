<?php

namespace App\Command;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait ClientTrait
{
    private ?HttpClientInterface $client = null;

    // todo: $this->parameterBag->get("env('BACKEND_HOST')");
    private string $hostUrl = 'http://api_backend:8000';

    #[Required]
    public function injectClient(HttpClientInterface $client): void
    {
        if (!$this->client) {
            $this->client = $client;
        }
    }
}
