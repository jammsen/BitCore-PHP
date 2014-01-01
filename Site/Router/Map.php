<?php

/**
 *
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * 
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): Map.php
 * @package     Site/Router/Map
 * @category    Router
 */
class Map implements IDebuggable {

    CONST MAP_ROUTE_SINGEL = 0;
    CONST MAP_ROUTE_ATTACH = 1;

    /**
     * 
     * Currently processing this attached common route information.
     * 
     * @var array
     * 
     */
    protected static $attach_common = null;

    /**
     * 
     * Currently processing these attached routes.
     * 
     * @var array
     * 
     */
    protected static $attach_routes = null;

    /**
     * 
     * Route definitions; these will be converted into objects.
     * 
     * @var array
     * 
     */
    protected static $definitions = array();

    /**
     * 
     * A RouteFactory for creating route objects.
     * 
     * @var RouteFactory
     * 
     */
    protected static $route_factory = array('RouteFactory', 'newInstance');

    /**
     * 
     * A RouteFactory for creating route objects.
     * 
     * @var RouteFactory
     * 
     */
    protected static $definition_factory = array('DefinitionFactory', 'newInstance');

    /**
     * 
     * Route objects created from the definitons.
     * 
     * @var array
     * 
     */
    protected static $routes = array();

    /**
     * 
     * Logging information about which routes were attempted to match.
     * 
     * @var array
     * 
     */
    protected static $log = array();

    /**
     * 
     * ActivLogging informations.
     * 
     * @var bool
     * 
     */
    protected static $_debug = false;

    public static function isDebuggable() {
        return self::$_debug;
    }

    public static function setDebuggable($bool) {
        self::$_debug = $bool;
    }

    /**
     * 
     * Adds a single route definition to the stack.
     * 
     * @param string $name The route name for `generate()` lookups.
     * 
     * @param string $path The route path.
     * 
     * @param array $spec The rest of the route definition, with keys for
     * `params`, `values`, etc.
     * 
     * @return void
     * 
     */
    public static function addRoute($name, $path, array $spec = null) {
        $spec['name'] = $name;
        $spec['path'] = $path;

        // these should be set only by the map
        unset($spec['name_prefix']);
        unset($spec['path_prefix']);

        // append to the route definitions
        $factory = self::$definition_factory;
        self::$definitions[] = $factory(self::MAP_ROUTE_SINGEL, $spec);
    }

    /**
     * 
     * Attaches several routes at once to a specific path prefix.
     * 
     * @param string $path_prefix The path that the routes should be attached
     * to.
     * 
     * @param array $spec An array of common route information, with an
     * additional `routes` key to define the routes themselves.
     * 
     * @return void
     * 
     */
    public static function attachRoutes($path_prefix, $spec) {
        $factory = self::$definition_factory;
        self::$definitions[] = $factory(self::MAP_ROUTE_ATTACH, $spec, $path_prefix);
    }

    /**
     * 
     * Gets a route that matches a given path and other server conditions.
     * 
     * @param string $path The path to match against.
     * 
     * @param array $server An array copy of $_SERVER.
     * 
     * @return Route|false Returns a Route object when it finds a match, or 
     * boolean false if there is no match.
     * 
     */
    public static function matchRoute($path, array $server, $standart = 'e404') {
        // reset the log
        if (self::$_debug)
            self::$log = array();

        // look through existing route objects
        foreach (self::$routes as $route) {
            if (self::$_debug)
                self::logRoute($route);
            if ($route->isMatch($path, $server)) {
                return $route;
            }
        }
        // convert remaining definitions as needed
        while (self::$attach_routes || self::$definitions) {
            $route = self::createNextRoute();
            if (self::$_debug)
                self::logRoute($route);
            if ($route->isMatch($path, $server)) {
                if (!$route->match_force)
                    return $route;

                $standart = $route->match_force;
            }
        }

        // no joy
        if (isset(self::$routes[$standart]))
            return self::$routes[$standart];
        return false;
    }

    /**
     * 
     * Looks up a route by name, and interpolates data into it to return
     * a URI path.
     * 
     * @param string $name The route name to look up.
     * 
     * @param array $data The data to interpolate into the URI; data keys
     * map to param tokens in the path.
     * 
     * @return string|false A URI path string if the route name is found, or
     * boolean false if not.
     * 
     */
    public static function generateUrl($name, $data = null) {
        // do we already have the route object?
        if (isset(self::$routes[$name])) {
            return self::$routes[$name]->generate($data);
        }

        // convert remaining definitions as needed
        while (self::$attach_routes || self::$definitions) {
            $route = self::createNextRoute();
            if ($route->name == $name) {
                return $route->generate($data);
            }
        }
        return;
        $v = self::$routes;

        echo $name;
        throw new PrintNiceException($v);
        // no joy
        throw new RouteNotFound($name);
    }

    /**
     * 
     * Reset the map to use an array of Route objects.
     * 
     * @param array $routes Use this array of route objects, likely generated
     * from `getRoutes()`.
     * 
     * @return void
     * 
     */
    public static function setRoutes(array $routes) {
        self::$routes = $routes;
        self::$definitions = array();
        self::$attach_common = array();
        self::$attach_routes = array();
    }

    /**
     * 
     * Get the array of Route objects in this map, likely for caching and
     * re-setting via `setRoutes()`.
     * 
     * @return array
     * 
     */
    public static function getRoutes() {
        // convert remaining definitions as needed
        while (self::$attach_routes || self::$definitions) {
            self::createNextRoute();
        }
        return self::$routes;
    }

    /**
     * 
     * Get the log of attempted route matches.
     * 
     * @return array
     * 
     */
    public static function getLog() {
        return self::$log;
    }

    /**
     * 
     * Add a route to the log of attempted matches.
     * 
     * @param Route $route Route object
     * 
     * @return array
     * 
     */
    protected static function logRoute(Route &$route) {
        self::$log[] = $route;
    }

    /**
     * 
     * Gets the next Route object in the stack, converting definitions to 
     * Route objects as needed.
     * 
     * @return Route|false A Route object, or boolean false at the end of the 
     * stack.
     * 
     */
    protected static function createNextRoute() {
        // do we have attached routes left to process?
        if (self::$attach_routes) {
            // yes, get the next attached definition
            $spec = self::getNextAttach();
        } else {
            // no, get the next unattached definition
            $spec = self::getNextDefinition();
        }

        // create a route object from it
        $factory = self::$route_factory;
        $route = $factory($spec);

        // retain the route object ...
        $name = $route->name;
        if ($name) {
            // ... under its name so we can look it up later
            self::$routes[$name] = $route;
        } else {
            // ... under no name, which means we can't look it up later
            self::$routes[] = $route;
        }

        // return whatever route got retained
        return $route;
    }

    /**
     * 
     * Gets the next route definition from the stack.
     * 
     * @return array A route definition.
     * 
     */
    protected static function getNextDefinition() {
        // get the next definition and extract the definition type
        $def = array_shift(self::$definitions);
        $spec = $def->getSpec();
        $type = $def->getType();

        // is it a 'single' definition type?
        if ($type == 'single') {
            // done!
            return $spec;
        }

        // it's an 'attach' definition; set up for attach processing.
        // retain the routes from the array ...
        self::$attach_routes = $spec['routes'];
        unset($spec['routes']);

        // ... and the remaining common information
        self::$attach_common = $spec;

        // reset the internal pointer of the array to avoid misnamed routes
        reset(self::$attach_routes);

        // now get the next attached route
        return self::getNextAttach();
    }

    /**
     * 
     * Gets the next attached route definition.
     * 
     * @return array A route definition.
     * 
     */
    protected static function getNextAttach() {
        $key = key(self::$attach_routes);
        $val = array_shift(self::$attach_routes);

        // which definition form are we using?
        if (is_string($key) && is_string($val)) {
            // short form, named in key
            $spec = [
                'name' => $key,
                'path' => $val,
                'values' => [
                    'action' => $key,
                ],
            ];
        } elseif (is_int($key) && is_string($val)) {

            // short form, no name
            $spec = [
                'name' => $key,
                'path' => $val,
            ];
        } elseif (is_string($key) && is_array($val)) {
            // long form, named in key
            $spec = $val;
            $spec['name'] = $key;
            // if no action, use key
            if (!isset($spec['values']['action'])) {
                $spec['values']['action'] = $key;
            }
        } elseif (is_int($key) && is_array($val)) {
            // long form, no name
            $spec = $val;
        } else {
            throw new UnexpectedType("route_spec_shoul_string_or_array", $key); //Route spec for '$key' should be a string or array."
        }

        // unset any path or name prefix on the spec itself
        unset($spec['name_prefix']);
        unset($spec['path_prefix']);

        // now merge with the attach info
        $spec = array_merge_recursive(self::$attach_common, $spec);

        // done!
        return $spec;
    }

}
