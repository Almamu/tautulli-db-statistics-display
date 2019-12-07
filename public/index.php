<?php
    const SETTINGS_FILE = __DIR__ . '/../dist/config.php';

    try
    {
        require __DIR__ . '/../vendor/autoload.php';

        $container = new DI\Container ();

        $webApplication = $container->get (\Almamu\Applications\WebInterface\WebInterface::class);

        if (file_exists (SETTINGS_FILE) === false)
        {
            // register a dummy website configuration for the templating service to run
            $container->set (
                \Almamu\Applications\Configuration\Website::class,
                \Almamu\Applications\Configuration\Website::fromArray (
                    array (
                        'public' => dirname ($_SERVER ['PHP_SELF']),
                        'basepath' => dirname (__DIR__)
                    )
                )
            );

            $webApplication ('configuration');
        }
        else
        {
            // settings are available, setup the environment
            $settings = require SETTINGS_FILE;

            if ($settings ['mode'] === 'api')
            {
                // register the settings object
                $container->set (
                    \Almamu\Configuration\TautulliAPI::class,
                    \Almamu\Configuration\TautulliAPI::fromArray ($settings ['settings'])
                );
                // register the data provider object
                $container->set (
                    \Almamu\DataProviders\DataProvider::class,
                    $container->get (\Almamu\DataProviders\TautulliAPI::class)
                );
            }
            elseif ($settings ['mode'] === 'sqlite')
            {
                throw new \Exception ('SQLite support not added yet');
            }

            // register the website configuration
            $container->set (
                \Almamu\Applications\Configuration\Website::class,
                \Almamu\Applications\Configuration\Website::fromArray ($settings ['website'])
            );

            $controller = $_GET ['controller'] ?? 'userlist';
            $webApplication ($controller);
        }
    }
    catch (\Exception $ex)
    {
        echo "Error detected, cannot display page: {$ex->getMessage ()}";
    }