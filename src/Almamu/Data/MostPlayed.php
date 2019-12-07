<?php
    namespace Almamu\Data;

    class MostPlayed
    {
        private $playCount = 0;
        private $ratingKey = 0;

        function __construct (int $playCount, int $ratingKey)
        {
            $this->playCount = $playCount;
            $this->ratingKey = $ratingKey;
        }

        /**
         * @return int
         */
        public function getPlayCount (): int
        {
            return $this->playCount;
        }

        /**
         * @param int $playCount
         * @return MostPlayed
         */
        public function setPlayCount (int $playCount): MostPlayed
        {
            $this->playCount = $playCount;
            return $this;
        }

        /**
         * @return int
         */
        public function getRatingKey(): int
        {
            return $this->ratingKey;
        }

        /**
         * @param int $ratingKey
         * @return MostPlayed
         */
        public function setRatingKey(int $ratingKey): MostPlayed
        {
            $this->ratingKey = $ratingKey;
            return $this;
        }
    }