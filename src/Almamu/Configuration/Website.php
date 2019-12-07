<?php
    namespace Almamu\Applications\Configuration;

    class Website
    {
        /** @var string The public URL the application is installed to */
        private $publicUrl = "";
        /** @var string The basepath to the application */
        private $basepath = "";

        protected function __construct (string $publicUrl, string $basepath)
        {
            $this
                ->setPublicUrl ($publicUrl)
                ->setBasepath ($basepath);
        }

        /**
         * @return string
         */
        public function getPublicUrl (): string
        {
            return $this->publicUrl;
        }

        /**
         * @param string $publicUrl
         *
         * @return $this
         */
        public function setPublicUrl (string $publicUrl)
        {
            $this->publicUrl = rtrim ($publicUrl, '/');

            return $this;
        }

        /**
         * @return string
         */
        public function getBasepath (): string
        {
            return $this->basepath;
        }

        /**
         * @param string $basepath
         *
         * @return $this
         */
        public function setBasepath (string $basepath): Website
        {
            $this->basepath = $basepath;
            return $this;
        }

        /**
         * Generates a new Website configuration from an array
         *
         * @param array<string, string> $parameters The information array
         *
         * @return self
         *
         * @throws \Exception If the configuration is not valid
         */
        static function fromArray (array $parameters): self
        {
            if (array_key_exists ('public', $parameters) === false)
                throw new \Exception ('The public URL for the website is required');
            if (array_key_exists ('basepath', $parameters) === false)
                throw new \Exception ('The basepath to the application is required');

            return new self (
                $parameters ['public'],
                $parameters ['basepath']
            );
        }

        /**
         * @return array The settings to use for this configuration container
         */
        function getSettings (): array
        {
            return array (
                'public' => $this->getPublicUrl (),
                'basepath' => $this->getBasepath ()
            );
        }
    };