<?php
    namespace Almamu\Data;

    class User
    {
        /** @var string The user's friendly name to be used on display */
        private $username = "";
        /** @var int The user's ID used to get information related to the user */
        private $userId = 0;
        /** @var string The user's thumbnail URL */
        private $thumbnail = "";

        function __construct (int $userId, string $username, string $thumbnail)
        {
            $this
                ->setUserId ($userId)
                ->setUsername ($username)
                ->setThumbnail ($thumbnail);
        }

        /**
         * @return string
         */
        public function getUsername (): string
        {
            return $this->username;
        }

        /**
         * @param string $username
         * @return $this
         */
        public function setUsername (string $username)
        {
            $this->username = $username;

            return $this;
        }

        /**
         * @return int
         */
        public function getUserId (): int
        {
            return $this->userId;
        }

        /**
         * @param int $userId
         * @return $this
         */
        public function setUserId (int $userId)
        {
            $this->userId = $userId;

            return $this;
        }

        /**
         * @return string
         */
        public function getThumbnail(): string
        {
            return $this->thumbnail;
        }

        /**
         * @param string $thumbnail
         * @return $this
         */
        public function setThumbnail(string $thumbnail)
        {
            $this->thumbnail = $thumbnail;

            return $this;
        }
    };