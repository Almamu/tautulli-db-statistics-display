<?php
    namespace Almamu\Configuration;

    class TautulliAPI
    {
        private $baseUrl = "";
        private $apiKey = "";
        private $requestTimeout = 30;

        function getApiKey (): string
        {
            return $this->apiKey;
        }

        function getURL (): string
        {
            return $this->baseUrl;
        }

        function getRequestTimeout (): int
        {
            return $this->requestTimeout;
        }

        function getURLForMethod (string $method)
        {
            return "{$this->getUrl ()}/api/v2?apikey={$this->getApiKey ()}&cmd={$method}";
        }

        function __construct (string $url, string $apiKey, int $requestTimeout = 30)
        {
            $this->requestTimeout = $requestTimeout;
            $this->apiKey = $apiKey;
            $this->baseUrl = $url;
        }

        static function fromArray (array $settings)
        {
            if (array_key_exists ('url', $settings) === false)
                throw new \Exception ("The 'url' key doesn't exist in the configuration");
            if (array_key_exists ('api', $settings) === false)
                throw new \Exception ("The 'api' key doesn't exist in the configuration");

            $baseUrl = $settings ['url'] ?? '';
            $apiKey = $settings ['api'] ?? '';
            $requestTimeout = $settings ['timeout'] ?? 3;

            return new self (
                $baseUrl, $apiKey, $requestTimeout
            );
        }

        /**
         * @return array The settings to use for this configuration container
         */
        function getSettings (): array
        {
            return array (
                'url' => $this->getURL (),
                'api' => $this->getApiKey (),
                'timeout' => $this->getRequestTimeout ()
            );
        }
    };