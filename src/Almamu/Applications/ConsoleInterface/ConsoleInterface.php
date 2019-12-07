<?php
    namespace Almamu\Applications\ConsoleInterface;

    class App extends \Symfony\Component\Console\Application
    {
        function __construct ()
        {
            parent::__construct ("TautulliStatistics", "1.0");
        }
    };