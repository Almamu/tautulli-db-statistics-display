<?php
    namespace Almamu\Connectors;

    use Almamu\Configuration\TautulliAPI as TautulliAPIConfiguration;
    use Almamu\Data\User;

    class TautulliAPI
    {
        private $configuration = null;

        function __construct (TautulliAPIConfiguration $configuration)
        {
            $this->configuration = $configuration;
        }

        /**
         * Prepares a CURL connection to the API with the given information
         *
         * @param string $method
         * @param array $parameters
         *
         * @return false|resource
         * @throws \Exception
         */
        private function prepareCURL (string $method, array $parameters = array ())
        {
            $url = $this->configuration->getURLForMethod ($method) . '&' . http_build_query ($parameters);
            $curl = curl_init ($url);

            if ($curl === false)
                throw new \Exception ('Cannot perform curl_init');

            curl_setopt ($curl, CURLOPT_TIMEOUT, $this->configuration->getRequestTimeout ());
            curl_setopt ($curl, CURLOPT_RETURNTRANSFER, true);

            return $curl;
        }

        /**
         * Performs a request to the Tautulli API and parses the result
         *
         * @param string $method The method to call on the API
         * @param array $parameters The extra parameters for the URL
         *
         * @return mixed The parsed JSON response
         * @throws \Exception If the request cannot be performed for any reason
         */
        private function performCURL (string $method, array $parameters = array ())
        {
            $curl = $this->prepareCURL ($method, $parameters);

            $result = curl_exec ($curl);

            if ($result === false)
            {
                $errno = curl_errno ($curl);
                $error = curl_error ($curl);

                curl_close ($curl);

                throw new \Exception ("Cannot perform curl request, error ({$errno}): {$error}");
            }

            curl_close ($curl);

            $json = json_decode ($result, true);

            if (is_array ($json) === false)
                throw new \Exception ('Unexpected server response');
            if (array_key_exists ('response', $json) === false)
                throw new \Exception ('Expected response');
            if (array_key_exists ('result', $json ['response']) === false)
                throw new \Exception ('Expected result key on response');
            if ($json ['response'] ['result'] !== 'success')
                throw new \Exception ("Expected success response, but got {$json ['result']}");
            if (array_key_exists ('data', $json ['response']) === false)
                throw new \Exception ('Expected result with data');

            return $json ['response'] ['data'];
        }

        /**
         * Obtains a list of all the available users in the system
         *
         * @return \Almamu\Data\User []
         * @throws \Exception If any error happened during the request
         */
        function getUsers (): array
        {
            $userlist = $this->performCURL ('get_users');

            if (is_array ($userlist) === false)
                throw new \Exception ('Unexpected API response, expected array of users');

            $result = array ();

            foreach ($userlist as $user)
            {
                // skip local user
                if ($user ['user_id'] == 0)
                    continue;

                $result [] = new User (
                    $user ['user_id'], $user ['username'], $user ['thumb'] ?? ''
                );
            }

            return $result;
        }

        /**
         * Allows fetching a history page for the given user
         *
         * @param int $userId
         * @param int $row
         * @param int $maximum
         * @return array
         * @throws \Exception
         */
        function getHistory (int $userId, int $row = 0, int $maximum = 25): array
        {
            $history = $this->performCURL (
                'get_history',
                array (
                    'start' => $row,
                    'length' => $maximum,
                    'user_id' => $userId
                )
            );

            if (is_array ($history) === false || array_key_exists ('data', $history) === false)
                throw new \Exception ('Unexpected API response, expected array of users');

            return $history ['data'];
        }

        function getMetadata (int $ratingKey): array
        {
            $metadata = $this->performCURL (
                'get_metadata',
                array (
                    'rating_key' => $ratingKey
                )
            );

            if (is_array ($metadata) === false)
                throw new \Exception ('Unexpected API response, expected array of users');

            return $metadata;
        }
    };