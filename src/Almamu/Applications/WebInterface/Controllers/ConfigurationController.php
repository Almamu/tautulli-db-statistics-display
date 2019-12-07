<?php
    namespace Almamu\Applications\WebInterface\Controllers;

    use Almamu\Applications\Configuration\Website;
    use Almamu\Applications\WebInterface\TemplateService;
    use Almamu\Configuration\TautulliAPI;
    use DI;

    class ConfigurationController
    {
        private $configuration;
        private $dependencyInjector;
        private $templateService;

        function __construct (TemplateService $templateService, Website $configuration, DI\Container $dependencyInjector)
        {
            $this->dependencyInjector = $dependencyInjector;
            $this->templateService = $templateService;
            $this->configuration = $configuration;
        }

        function show ()
        {
            if ($_SERVER ['REQUEST_METHOD'] === 'POST')
            {
                $this->handleSetSettings ();
            }
            else
            {
                $this->handleSettingsForm ();
            }
        }

        function handleSetSettings ()
        {
            $errorlist = array ();

            try
            {
                // ensure the data is correct
                if (array_key_exists ('mode', $_POST) === false)
                    $errorlist [] = 'Please select an operation mode';

                if ($_POST ['mode'] === 'api')
                {
                    if (array_key_exists ('url', $_POST) === false || empty ($_POST ['url']) === true)
                        $errorlist [] = 'You must specify the public URL to the Tautulli API';
                    if (array_key_exists ('key', $_POST) === false || empty ($_POST ['key']) === true)
                        $errorlist [] = 'You must specify an API key to connect to the Tautulli API';
                }
                elseif ($_POST ['mode'] === 'sqlite')
                {
                    if (array_key_exists ('path', $_POST) === false || empty ($_POST ['path']) === true)
                        $errorlist [] = 'You must specify the path to the SQLite file';

                    // ensure the file exists
                    if (file_exists ($_POST ['path']) === false)
                        $errorlist [] = 'The sqlite database doesn\'t exist on the given path';

                    $errorlist [] = 'SQLite mode not supported yet!';
                }
                else
                {
                    $errorlist [] = 'Unknown mode';
                }

                // ensure application configuration is correct
                if (array_key_exists ('basepath', $_POST) === false || empty ($_POST ['basepath']) === true)
                    $errorlist [] = 'You must specify the base path to the application\' main folder';
                if (array_key_exists ('publicurl', $_POST) === false || empty ($_POST ['publicurl']) === true)
                    $errorlist [] = 'You must specify the public URL to the application';

                if (count ($errorlist) > 0)
                    throw new \Exception ('');

                $configurationArray = array ();

                // configuration is correct, perform the configuration generation
                $websiteConfiguration = $this->dependencyInjector->get (Website::class);

                $websiteConfiguration
                    ->setBasepath ($_POST ['basepath'])
                    ->setPublicUrl ($_POST ['publicurl']);

                // based on the mode setup the correct configurator
                if ($_POST ['mode'] === 'api')
                {
                    $apiConfiguration = new TautulliAPI (
                        $_POST ['url'], $_POST ['key']
                    );

                    $configurationArray ['mode'] = 'api';
                    $configurationArray ['settings'] = $apiConfiguration->getSettings ();

                    // TODO: TEST API
                }
                elseif ($_POST ['mode'] === 'sqlite')
                {
                    // TODO: ADD CODE FOR SQLITE SETTINGS
                }

                $configurationArray ['website'] = $this->configuration->getSettings ();

                // with the new, correct path we can be sure of writing to the correct folder
                // ensure the config folder exists
                $configPath = rtrim ($this->configuration->getBasepath (), '/') . '/dist/';

                if (file_exists ($configPath) === false)
                {
                    mkdir ($configPath, 640);
                }

                if (is_dir ($configPath) === false)
                {
                    $errorlist [] = 'Error creating the config folder on ' . $configPath;
                    throw new \Exception ('');
                }

                $configFile = $configPath . 'config.php';

                $result = file_put_contents (
                    $configFile,
                    '<?php return ' . var_export ($configurationArray, true) . ';'
                );

                if ($result === false)
                {
                    $errorlist [] = 'Error creating the configuration file...';
                    throw new \Exception ('');
                }
            }
            catch (\Exception $ex)
            {
                return $this->templateService->render (
                    'settings',
                    array (
                        'basepath' => $this->configuration->getBasepath (),
                        'errors' => $errorlist,
                        'config' => $_POST
                    )
                );
            }

            // finally render the settings ok template
            return $this->templateService->render ('installation-completed');
        }

        function handleSettingsForm ()
        {
            return $this->templateService->render (
                'settings',
                array (
                    'basepath' => $this->configuration->getBasepath ()
                )
            );
        }
    };