<?php
    namespace Almamu\Applications\WebInterface;

    use Almamu\Applications\WebInterface\Controllers\ConfigurationController;
    use Almamu\Applications\WebInterface\Controllers\GenerateController;
    use Almamu\Applications\WebInterface\Controllers\UserListController;
    use DI;

    class WebInterface
    {
        private $dependencyInjector;
        /** @var array List of controllers available for the website */
        private $controllers = [];

        function __construct (DI\Container $dependencyInjector)
        {
            $this->dependencyInjector = $dependencyInjector;

            $this
                ->registerController ('userlist', UserListController::class, 'show')
                ->registerController ('configuration', ConfigurationController::class, 'show')
                ->registerController ('generate', GenerateController::class, 'show');
        }

        private function registerController (string $controllerName, string $className, string $function): self
        {
            if (class_exists ($className) === false)
                throw new \Exception ('Registering a non-existant class as controller is not allowed');
            if (method_exists ($className, $function) === false)
                throw new \Exception ('Registering a non-existant method as controller is not allowed');

            $this->controllers [$controllerName] = array (
                'class' => $className,
                'function' => $function
            );

            return $this;
        }

        function __invoke (string $controller)
        {
            if (array_key_exists ($controller, $this->controllers) === false)
                throw new \Exception ("The controller {$controller} doesn't exist");

            // get the controller's information first
            $controllerInfo = $this->controllers [$controller];
            // get the controller trough dependency injection
            $controllerObject = $this->dependencyInjector->get ($controllerInfo ['class']);
            // finally call the controller's function
            return call_user_func (array ($controllerObject, $controllerInfo ['function']));
        }
    };