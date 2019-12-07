<?php
    namespace Almamu\Data;

    class PlayTime
    {
        private $totalTime;
        private $ratingKey;

        function __construct (int $totalTime, int $ratingKey)
        {
            $this->totalTime = $totalTime;
            $this->ratingKey = $ratingKey;
        }

        /**
         * @return mixed
         */
        public function getTotalTime()
        {
            return $this->totalTime;
        }

        /**
         * @param mixed $totalTime
         * @return PlayTime
         */
        public function setTotalTime($totalTime)
        {
            $this->totalTime = $totalTime;
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
         * @return PlayTime
         */
        public function setRatingKey(int $ratingKey): PlayTime
        {
            $this->ratingKey = $ratingKey;
            return $this;
        }
    };