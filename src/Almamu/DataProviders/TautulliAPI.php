<?php
    namespace Almamu\DataProviders;

    use Almamu\Connectors\TautulliAPI as Connector;
    use Almamu\Data\Metadata;
    use Almamu\Data\MostPlayed;
    use Almamu\Data\PlayTime;

    class TautulliAPI implements DataProvider
    {
        /** @var Connector The api connector to read data from */
        private $connector;
        /** @var array<int, array<object>> The history cache */
        private $historyCache = [];

        function __construct (Connector $connector)
        {
            $this->connector = $connector;
        }

        private function fetchHistory (int $userId): array
        {
            if (array_key_exists ($userId, $this->historyCache) === true)
                return $this->historyCache [$userId];

            $currentRow = 0;
            $rowsPerPage = 2000;

            $this->historyCache [$userId] = [];

            // fetch the history a thousand entries at a time
            while (true)
            {
                $entries = $this->connector->getHistory ($userId, $currentRow, $rowsPerPage);

                // add the history entries to the list
                $this->historyCache [$userId] = array_merge ($this->historyCache [$userId], $entries);

                if (count ($entries) !== $rowsPerPage)
                    break;

                $currentRow += $rowsPerPage;
            }

            return $this->historyCache [$userId];
        }

        /** @inheritdoc */
        function listRegisteredUsers (): array
        {
            return $this->connector->getUsers ();
        }

        /** @inheritDoc */
        function getMostPlayed (int $userId, int $minimumTime, int $maximumTime, string $mediaType): array
        {
            $userHistory = $this->fetchHistory ($userId);
            /** @var array<int, MostPlayed> $playedList */
            $playedList = [];

            foreach ($userHistory as $historyEntry)
            {
                // ignore entries out of time
                if ($historyEntry ['started'] < $minimumTime || $historyEntry ['started'] > $maximumTime)
                    continue;
                if ($historyEntry ['media_type'] != $mediaType)
                    continue;

                if (array_key_exists ($historyEntry ['rating_key'], $playedList) === false)
                    $playedList [$historyEntry ['rating_key']] = new MostPlayed (
                        0, $historyEntry ['rating_key']
                    );

                $mostPlayed = $playedList [$historyEntry ['rating_key']];
                $mostPlayed->setPlayCount (
                    $mostPlayed->getPlayCount () + 1
                );
            }

            // get rid of the keys so we have a normal array
            $playedList = array_values ($playedList);
            $length = count ($playedList);

            for ($i = 0; $i < $length - 1; $i ++)
            {
                for ($j = 0; $j < $length - $i - 1; $j++)
                {
                    if ($playedList [$j]->getPlayCount () < $playedList [$j + 1]->getPlayCount ())
                    {
                        $tmp = $playedList [$j];
                        $playedList [$j] = $playedList [$j + 1];
                        $playedList [$j + 1] = $tmp;
                    }
                }
            }

            return $playedList;
        }

        /** @inheritDoc */
        function getMostPlayedByGrandparent (int $userId, int $minimumTime, int $maximumTime, string $mediaType): array
        {
            $userHistory = $this->fetchHistory ($userId);
            /** @var array<int, MostPlayed> $playedList */
            $playedList = [];

            foreach ($userHistory as $historyEntry)
            {
                // ignore entries out of time
                if ($historyEntry ['started'] < $minimumTime || $historyEntry ['started'] > $maximumTime)
                    continue;
                if ($historyEntry ['media_type'] != $mediaType)
                    continue;

                if (array_key_exists ($historyEntry ['grandparent_rating_key'], $playedList) === false)
                    $playedList [$historyEntry ['grandparent_rating_key']] = new MostPlayed (
                        0, $historyEntry ['grandparent_rating_key']
                    );

                $mostPlayed = $playedList [$historyEntry ['grandparent_rating_key']];
                $mostPlayed->setPlayCount (
                    $mostPlayed->getPlayCount () + 1
                );
            }

            // get rid of the keys so we have a normal array
            $playedList = array_values ($playedList);
            $length = count ($playedList);

            for ($i = 0; $i < $length - 1; $i ++)
            {
                for ($j = 0; $j < $length - $i - 1; $j++)
                {
                    if ($playedList [$j]->getPlayCount () < $playedList [$j + 1]->getPlayCount ())
                    {
                        $tmp = $playedList [$j];
                        $playedList [$j] = $playedList [$j + 1];
                        $playedList [$j + 1] = $tmp;
                    }
                }
            }

            return $playedList;
        }

        /** @inheritDoc */
        function getMostPlayedByParent (int $userId, int $minimumTime, int $maximumTime, string $mediaType): array
        {
            $userHistory = $this->fetchHistory ($userId);
            /** @var array<int, MostPlayed> $playedList */
            $playedList = [];

            foreach ($userHistory as $historyEntry)
            {
                // ignore entries out of time
                if ($historyEntry ['started'] < $minimumTime || $historyEntry ['started'] > $maximumTime)
                    continue;
                if ($historyEntry ['media_type'] != $mediaType)
                    continue;

                if (array_key_exists ($historyEntry ['parent_rating_key'], $playedList) === false)
                    $playedList [$historyEntry ['parent_rating_key']] = new MostPlayed (
                        0, $historyEntry ['parent_rating_key']
                    );

                $mostPlayed = $playedList [$historyEntry ['parent_rating_key']];
                $mostPlayed->setPlayCount (
                    $mostPlayed->getPlayCount () + 1
                );
            }

            // get rid of the keys so we have a normal array
            $playedList = array_values ($playedList);
            $length = count ($playedList);

            for ($i = 0; $i < $length - 1; $i ++)
            {
                for ($j = 0; $j < $length - $i - 1; $j++)
                {
                    if ($playedList [$j]->getPlayCount () < $playedList [$j + 1]->getPlayCount ())
                    {
                        $tmp = $playedList [$j];
                        $playedList [$j] = $playedList [$j + 1];
                        $playedList [$j + 1] = $tmp;
                    }
                }
            }

            return $playedList;
        }

        /** @inheritDoc */
        function getMostPlaytime (int $userId, int $minimumTime, int $maximumTime, string $mediaType): array
        {
            $userHistory = $this->fetchHistory ($userId);
            /** @var array<int, PlayTime> $playedList */
            $playedList = [];

            foreach ($userHistory as $historyEntry)
            {
                // ignore entries out of time
                if ($historyEntry ['started'] < $minimumTime || $historyEntry ['started'] > $maximumTime)
                    continue;
                if ($historyEntry ['media_type'] != $mediaType)
                    continue;

                if (array_key_exists ($historyEntry ['rating_key'], $playedList) === false)
                    $playedList [$historyEntry ['rating_key']] = new PlayTime (
                        0, $historyEntry ['rating_key']
                    );

                $mostPlayed = $playedList [$historyEntry ['rating_key']];
                $mostPlayed->setTotalTime (
                    $mostPlayed->getTotalTime () + $historyEntry ['duration']
                );
            }

            // get rid of the keys so we have a normal array
            $playedList = array_values ($playedList);
            $length = count ($playedList);

            for ($i = 0; $i < $length - 1; $i ++)
            {
                for ($j = 0; $j < $length - $i - 1; $j++)
                {
                    if ($playedList [$j]->getTotalTime () < $playedList [$j + 1]->getTotalTime ())
                    {
                        $tmp = $playedList [$j];
                        $playedList [$j] = $playedList [$j + 1];
                        $playedList [$j + 1] = $tmp;
                    }
                }
            }

            return $playedList;
        }

        /** @inheritDoc */
        function getMostPlaytimeByGrandparent (int $userId, int $minimumTime, int $maximumTime, string $mediaType): array
        {
            $userHistory = $this->fetchHistory ($userId);
            /** @var array<int, PlayTime> $playedList */
            $playedList = [];

            foreach ($userHistory as $historyEntry)
            {
                // ignore entries out of time
                if ($historyEntry ['started'] < $minimumTime || $historyEntry ['started'] > $maximumTime)
                    continue;
                if ($historyEntry ['media_type'] != $mediaType)
                    continue;

                if (array_key_exists ($historyEntry ['grandparent_rating_key'], $playedList) === false)
                    $playedList [$historyEntry ['grandparent_rating_key']] = new PlayTime (
                        0, $historyEntry ['grandparent_rating_key']
                    );

                $mostPlayed = $playedList [$historyEntry ['grandparent_rating_key']];
                $mostPlayed->setTotalTime (
                    $mostPlayed->getTotalTime () + $historyEntry ['duration']
                );
            }

            // get rid of the keys so we have a normal array
            $playedList = array_values ($playedList);
            $length = count ($playedList);

            for ($i = 0; $i < $length - 1; $i ++)
            {
                for ($j = 0; $j < $length - $i - 1; $j++)
                {
                    if ($playedList [$j]->getTotalTime () < $playedList [$j + 1]->getTotalTime ())
                    {
                        $tmp = $playedList [$j];
                        $playedList [$j] = $playedList [$j + 1];
                        $playedList [$j + 1] = $tmp;
                    }
                }
            }

            return $playedList;
        }

        /** @inheritDoc */
        function getMostPlaytimeByParent (int $userId, int $minimumTime, int $maximumTime, string $mediaType): array
        {
            $userHistory = $this->fetchHistory ($userId);
            /** @var array<int, PlayTime> $playedList */
            $playedList = [];

            foreach ($userHistory as $historyEntry)
            {
                // ignore entries out of time
                if ($historyEntry ['started'] < $minimumTime || $historyEntry ['started'] > $maximumTime)
                    continue;
                if ($historyEntry ['media_type'] != $mediaType)
                    continue;

                if (array_key_exists ($historyEntry ['parent_rating_key'], $playedList) === false)
                    $playedList [$historyEntry ['parent_rating_key']] = new PlayTime (
                        0, $historyEntry ['parent_rating_key']
                    );

                $mostPlayed = $playedList [$historyEntry ['parent_rating_key']];
                $mostPlayed->setTotalTime (
                    $mostPlayed->getTotalTime () + $historyEntry ['duration']
                );
            }

            // get rid of the keys so we have a normal array
            $playedList = array_values ($playedList);
            $length = count ($playedList);

            for ($i = 0; $i < $length - 1; $i ++)
            {
                for ($j = 0; $j < $length - $i - 1; $j++)
                {
                    if ($playedList [$j]->getTotalTime () < $playedList [$j + 1]->getTotalTime ())
                    {
                        $tmp = $playedList [$j];
                        $playedList [$j] = $playedList [$j + 1];
                        $playedList [$j + 1] = $tmp;
                    }
                }
            }

            return $playedList;
        }

        function getMetadata (int $ratingKey): Metadata
        {
            $metadata = $this->connector->getMetadata ($ratingKey);

            return Metadata::fromArray ($metadata);
        }
    }