<?php
    namespace Almamu\Applications\WebInterface\Controllers;

    use Almamu\Applications\WebInterface\TemplateService;
    use Almamu\DataProviders\DataProvider;

    class GenerateController
    {
        private $templateService;
        private $provider;

        function __construct (TemplateService $templateService, DataProvider $provider)
        {
            $this->templateService = $templateService;
            $this->provider = $provider;
        }

        function show ()
        {
            $mostPlayed = $this->provider->getMostPlayed (
                $_GET ['userId'],
                strtotime ("first day of january this year"),
                time (),
                "track"
            );
            $mostPlaytime = $this->provider->getMostPlaytime (
                $_GET ['userId'],
                strtotime ("first day of january this year"),
                time (),
                "track"
            );
            $mostPlayedByParent = $this->provider->getMostPlayed (
                $_GET ['userId'],
                strtotime ("First day of january this year"),
                time (),
                "track"
            );
            $mostPlaytimeByParent = $this->provider->getMostPlaytimeByParent (
                $_GET ['userId'],
                strtotime ("first day of january this year"),
                time (),
                "track"
            );
            $mostPlayedByFrandparent = $this->provider->getMostPlayedByGrandparent (
                $_GET ['userId'],
                strtotime ("first day of january this year"),
                time (),
                "track"
            );
            $mostPlaytimeByGrandparent = $this->provider->getMostPlaytimeByGrandparent (
                $_GET ['userId'],
                strtotime ("first day of january this year"),
                time (),
                "track"
            );

            // get just 5 entries for each one
        }
    };