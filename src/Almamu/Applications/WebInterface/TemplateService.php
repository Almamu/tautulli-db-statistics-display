<?php
    namespace Almamu\Applications\WebInterface;

    use Almamu\Applications\Configuration\Website;

    class TemplateService
    {
        /** @var string The current block being rendered (if any) */
        private $currentblock = "";
        /** @var array<string, string> List of rendered blocks and their contents */
        private $blocks = [];
        /** @var Website The configuration for website app */
        private $configuration;
        /** @var array The list of functions available */
        private $functions = [];

        /**
         * @param Website $configuration
         *
         * @throws \Exception
         */
        function __construct (Website $configuration)
        {
            $this->configuration = $configuration;

            // register all the functions
            $this
                ->registerFunction ('showblock', 'showblock')
                ->registerFunction ('render', 'render')
                ->registerFunction ('startblock', 'startblock')
                ->registerFunction ('endblock', 'endblock')
                ->registerFunction ('parent', 'parent')
                ->registerFunction ('isblock', 'isblock')
                ->registerFunction ('parent', 'parent')
                ->registerFunction ('resource', 'resource')
                ->registerFunction ('url', 'url')
                ->registerFunction ('current', 'current')
                ->registerFunction ('action', 'action');
        }

        /**
         * Registers the given function for the templating engine to expose it
         *
         * @param string $name The function's name
         * @param string $function The function to register to
         *
         * @return $this
         *
         * @throws \Exception If the function doesn't exist
         */
        private function registerFunction (string $name, string $function)
        {
            if (method_exists ($this, $function) === false)
                throw new \Exception ("Cannot register functions that do not exist ({$function})");

            $this->functions [$name] = array ($this, $function);

            return $this;
        }

        /**
         * Renders an HTML page for the web application
         *
         * @param string $view The view to render
         * @param array $parameters The parameters to give to the view (if any)
         *
         * @return mixed
         * @throws \Exception
         */
        function render (string $view, array $parameters = array ())
        {
            $viewpath = $this->configuration->getBasePath () . '/views/' . $view . '.phtml';

            if (file_exists ($viewpath) === false)
                throw new \Exception ("Cannot render view, the given view ({$view}) doesn't exist");

            extract ($parameters);
            extract ($this->functions);

            return require $viewpath;
        }

        /**
         * Begins a block rendering
         *
         * @param string $name The name of the block
         *
         * @throws \Exception If the block name is empty or there is a block being rendered already
         */
        function startblock (string $name): void
        {
            if (empty ($name) == true)
                throw new \Exception ("The block name cannot be empty");

            if(empty ($this->currentblock) == false)
                throw new \Exception (
                    "Cannot start a block in a block. Block {$this->currentblock} is being rendered yet"
                );

            $this->currentblock = $name;

            // begin output buffering
            ob_start ();
        }

        /**
         * Completes the block rendering already being processed
         *
         * @return void
         *
         * @throws \Exception
         */
        function endblock ()
        {
            if (empty ($this->currentblock) == true)
                throw new \Exception ("Cannot end a block because we are not rendering anything");

            $ob = ob_get_clean ();

            if (is_string ($ob) === false)
                throw new \Exception ("Cannot perform ob_get_clean");

            $this->blocks [$this->currentblock] = $ob;
            $this->currentblock = "";
        }

        /**
         * Outputs the given block to the standard output
         *
         * @param string $block
         *
         * @return void
         *
         * @throws \Exception If the specified block is nowhere to be found
         */
        function showblock (string $block)
        {
            if (array_key_exists ($block, $this->blocks) == false)
                throw new \Exception("The given block {$block} cannot be displayed as it's not registered");

            echo $this->blocks [$block];
        }

        /**
         * Outputs the current block's registered content (empty if the block is not registered already)
         *
         * @return void
         *
         * @throws \Exception
         */
        function parent ()
        {
            if (array_key_exists ($this->currentblock, $this->blocks) == true)
                $this->showblock ($this->currentblock);
        }

        /**
         * @return bool If we're rendering a block or not
         */
        function isblock (): bool
        {
            return empty ($this->currentblock) == false;
        }

        /**
         * Generates a public path to the given resource
         *
         * @param string $name The name of the resource
         *
         * @return string The public path to the specified resource
         */
        function resource (string $name): string
        {
            $name = ltrim ($name, '/');

            return
                $this->configuration->getPublicUrl () .
                "/assets/{$name}";
        }

        /**
         * Generates a full URL to the given $path
         *
         * The parameter matching for $urlparams is done searching (and replacing)
         * the key name in the URL this way:
         * /hello/:name -> /hello/almamu
         *
         * given the following $urlparams: array('name' => 'almamu')
         *
         * @param string $path The path to generate the URL for
         * @param array<string, mixed> $params The GET params for the URL
         * @param array<string, string> $urlparams Parameters for the URL
         *
         * @return string The path to generate
         */
        function url (string $path, array $params = array (), array $urlparams = array ()): string
        {
            $getparams = ((count ($params) == 0 ) ? '' : '?');

            foreach($params as $key => $value)
            {
                $getparams .= $key . "=" . urlencode ((string) $value) . "&";
            }

            $endurlparams = array();
            $endurlvalues = array();

            foreach($urlparams as $key => $value)
            {
                $endurlparams [] = ":" . $key;
                $endurlvalues [] = $value;
            }

            $path = trim (str_replace ($endurlparams, $endurlvalues, $path), "/");

            return
                $this->configuration->getPublicUrl () .
                "/{$path}{$getparams}";
        }

        /**
         * @return string The current, full URL the user is currently at
         */
        function current (): string
        {
            /// TODO: USE REQUEST
            return $_SERVER ['REQUEST_URI'];
        }

        /**
         * Generates an URL to the given controller
         *
         * @param string $action
         * @param array $parameters
         *
         * @return string
         */
        function action (string $action, array $parameters = array ()): string
        {
            $parameters ['controller'] = $action;

            return $this->url ('/', $parameters);
        }
    };