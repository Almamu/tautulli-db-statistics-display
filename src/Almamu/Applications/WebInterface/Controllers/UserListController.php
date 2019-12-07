<?php
    namespace Almamu\Applications\WebInterface\Controllers;

    use Almamu\Applications\WebInterface\TemplateService;
    use Almamu\Applications\WebInterface\WebInterface;
    use Almamu\DataProviders\DataProvider;

    class UserListController
    {
        private $templateService;
        private $provider;

        function __construct (TemplateService $templateService, DataProvider $provider)
        {
            $this->templateService = $templateService;
            $this->provider = $provider;
        }

        /**
         * @throws \Exception
         */
        function show ()
        {
            return $this->templateService->render (
                'userlist',
                array (
                    'users' => $this->provider->listRegisteredUsers ()
                )
            );
        }
    };