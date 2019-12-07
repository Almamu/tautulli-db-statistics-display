<?php
    namespace Almamu\DataProviders;

    use Almamu\Data\Metadata;

    /**
     * Interface for obtaining information from different sources
     *
     * @author Alexis Maiquez Murcia <almamu@almamu.com>
     * @package Almamu\DataProviders
     */
    interface DataProvider
    {
        /**
         * @return \Almamu\Data\User []
         */
        function listRegisteredUsers (): array;

        /**
         * @param int $userId The user to get the most player for
         * @param int $minimumTime The minimum time to count an entry
         * @param int $maximumTime The maximum time to count an entry
         * @param string $mediaType The media type to count
         *
         * @return \Almamu\Data\MostPlayed []
         */
        function getMostPlayed (int $userId, int $minimumTime, int $maximumTime, string $mediaType): array;

        /**
         * @param int $userId The user to get the most player for
         * @param int $minimumTime The minimum time to count an entry
         * @param int $maximumTime The maximum time to count an entry
         * @param string $mediaType The media type to count
         *
         * @return \Almamu\Data\MostPlayed []
         */
        function getMostPlayedByGrandparent (int $userId, int $minimumTime, int $maximumTime, string $mediaType): array;

        /**
         * @param int $userId The user to get the most play time for
         * @param int $minimumTime The minimum time to count an entry
         * @param int $maximumTime The maximum time to count an entry
         * @param string $mediaType The media type to count
         *
         * @return \Almamu\Data\PlayTime []
         */
        function getMostPlaytime (int $userId, int $minimumTime, int $maximumTime, string $mediaType): array;

        /**
         * @param int $userId The user to get the most play time for
         * @param int $minimumTime The minimum time to count an entry
         * @param int $maximumTime The maximum time to count an entry
         * @param string $mediaType The media type to count
         *
         * @return \Almamu\Data\PlayTime []
         */
        function getMostPlaytimeByGrandparent (int $userId, int $minimumTime, int $maximumTime, string $mediaType): array;

        /**
         * @param int $userId The user to get the most play time for
         * @param int $minimumTime The minimum time to count an entry
         * @param int $maximumTime The maximum time to count an entry
         * @param string $mediaType The media type to count
         *
         * @return \Almamu\Data\PlayTime []
         */
        function getMostPlaytimeByParent (int $userId, int $minimumTime, int $maximumTime, string $mediaType): array;

        /**
         * @param int $ratingKey The media to get the metadata for
         *
         * @return Metadata
         */
        function getMetadata (int $ratingKey): Metadata;
    };