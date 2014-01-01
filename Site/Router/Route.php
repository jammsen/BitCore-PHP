<?php

/**
 * Represents an individual route with a name, path, params, values, etc.
 * 
 * In general, you should never need to instantiate a Route directly. Use the
 * RouteFactory instead, or the Map.
 * 
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * 
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): Route.php
 * @package     Site/Router/Route
 * @category    Router
 */
class Route extends ArrayObject {
    /*
     * 
     */

    /**
     * 
     * The $path property converted to a regular expression, using the $params
     * subpatterns.
     * 
     * @var string
     * 
     */
    protected $regex;

    /**
     * 
     * All param matches found in the path during the `isMatch()` process.
     * 
     * @var array
     * 
     * @see isMatch()
     * 
     */
    protected $matches;

    /**
     * 
     * Retain debugging information about why the route did not match.
     * 
     * @var array
     * 
     */
    protected $debug;

    /**
     * 
     * The name of the wildcard param, if any.
     * 
     * @var array
     * 
     */
    protected $wildcard;

    /**
     * 
     * Constructor.
     * 
     * @param string Array .
     * 
     * @return Route
     * 
     */
    public function __construct(&$params) {
        parent::__construct($params);

        if ($this['name_prefix'] && $this['name'])
            $this['name'] = $this['name_prefix'] . $this['name'];

        if ($this['path_prefix'] && strpos($this['path'], '://') === false)
            $this['path'] = str_replace('//', '/', $this['path_prefix'] . $this['path']);

        // convert path and params to a regular expression
        $this->setRegex();
    }

    /**
     * 
     * Magic read-only for all properties.
     * 
     * @param string $key The property to read from.
     * 
     * @return mixed
     * 
     */
    public function __get($key) {
        return $this[$key];
    }

    /**
     * 
     * Magic isset() for all properties.
     * 
     * @param string $key The property to check if isset().
     * 
     * @return bool
     * 
     */
    public function __isset($key) {
        return isset($this->$key);
    }

    /**
     * 
     * Checks if a given path and server values are a match for this
     * Route.
     * 
     * @param string $path The path to check against this Route.
     * 
     * @param array $server A copy of $_SERVER so that this Route can check 
     * against the server values.
     * 
     * @return bool
     * 
     */
    public function isMatch($path, array $server) {
        if (!$this['routable']) {
            if (Map::isDebuggable())
                $this->debug[] = 'Not routable.';
            return false;
        }

        $is_match = $this->isRegexMatch($path);
        $_is_match = $this->isMethodMatch($server) && $this->isSecureMatch($server) && $this->isCustomMatch($server);

        if ($this->match_force !== null && $is_match && !$_is_match)
            return true;


        if (!$is_match || !$_is_match)
            return false;


        // populate the path matches into the route values
        foreach ($this->matches as $key => $val) {
            if (is_string($key)) {
                $this['values'][$key] = rawurldecode($val);
            }
        }

        // is a wildcard param specified?
        if ($this->wildcard) {

            // are there are actual wildcard values?
            if (empty($this['values'][$this->wildcard])) {
                // no, set a blank array
                $this['values'][$this->wildcard] = [];
            } else {
                // yes, retain and rawurldecode them
                $this['values'][$this->wildcard] = array_map(
                        'rawurldecode', explode('/', $this->values[$this->wildcard])
                );
            }

            // backwards compat: rename "__wildcard__" to "*"
            if ($this->wildcard == '__wildcard__') {
                $this['values']['*'] = $this['values']['__wildcard__'];
                unset($this['values']['__wildcard__']);
            }
        }

        // done!
        return true;
    }

    /**
     * 
     * Gets the path for this Route with data replacements for param tokens.
     * 
     * @param array $data An array of key-value pairs to interpolate into the
     * param tokens in the path for this Route. Keys that do not map to
     * params are discarded; param tokens that have no mapped key are left in
     * place.
     * 
     * @return string
     * 
     */
    public function generate(array $data = null) {
        // use a callable to modify the path data?
        if ($this->generate) {
            $generate = $this->generate;
            $data = $generate($this, (array) $data);
        }

        // interpolate into the path
        $replace = array();
        $data = array_merge($this->values, (array) $data);
        foreach ($data as $key => $val) {
            // Closures can't be cast to string
            if (!($val instanceof Closure)) {
                $replace["{:$key}"] = rawurlencode($val);
            }
        }
        return strtr($this['path'], $replace);
    }

    /**
     * 
     * Sets the regular expression for this Route based on its params.
     * 
     * @return void
     * 
     */
    protected function setRegex() {
        // is a deprecated wildcard indicated at the end of the path?
        if (substr($this['path'], -2) == '/*') {
            // yes, replace it with a special token and regex
            $this['path'] = substr($this['path'], 0, -2) . "/{:__wildcard__:(.*)}";
            $this->wildcard = '__wildcard__';
        }

        // is a required wildcard indicated at the end of the path?
        $match = preg_match("/\/\{:([a-z_][a-z0-9_]+)\+\}$/i", $this['path'], $matches);
        if ($match) {
            $this->wildcard = $matches[1];
            $pos = strrpos($this['path'], $matches[0]);
            $this['path'] = substr($this['path'], 0, $pos) . "/{:{$this->wildcard}:(.+)}";
        }

        // is an optional wildcard indicated at the end of the path?
        $match = preg_match("/\/\{:([a-z_][a-z0-9_]+)\*\}$/i", $this['path'], $matches);
        if ($match) {
            $this->wildcard = $matches[1];
            $pos = strrpos($this['path'], $matches[0]);
            $this['path'] = substr($this['path'], 0, $pos) . "(/{:{$this->wildcard}:(.*)})?";
        }

        // now extract inline token params from the path. converts
        // {:token:regex} to {:token} and retains the regex in params.
        $find = "/\{:(.*?)(:(.*?))?\}/";
        preg_match_all($find, $this['path'], $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $whole = $match[0];
            $name = $match[1];
            if (isset($match[3])) {
                // there is an inline token pattern; retain it, overriding
                // the existing param ...
                $this['params'][$name] = $match[3];
                // ... and replace in the path without the pattern.
                $this['path'] = str_replace($whole, "{:$name}", $this['path']);
            } elseif (!isset($this['params'][$name])) {
                // use a default pattern when none exists
                $this['params'][$name] = "([^/]+)";
            }
        }

        // now create the regular expression from the path and param patterns
        $this->regex = $this['path'];
        if ($this['params']) {
            $keys = [];
            $vals = [];
            foreach ($this['params'] as $name => $subpattern) {
                if ($subpattern[0] != '(') {
                    $message = "Subpattern for param '$name' must start with '('.";
                    throw new Exception($message);
                } else {
                    $keys[] = "{:$name}";
                    $vals[] = "(?P<$name>" . substr($subpattern, 1);
                }
            }
            $this->regex = str_replace($keys, $vals, $this->regex);
        }
    }

    /**
     * 
     * Checks that the path matches the Route regex.
     * 
     * @param string $path The path to match against.
     * 
     * @return bool True on a match, false if not.
     * 
     */
    protected function isRegexMatch($path) {
        $regex = "#^{$this->regex}$#";
        $match = preg_match($regex, $path, $this->matches);
        if (!$match) {
            if (Map::isDebuggable())
                $this->debug[] = 'Not a regex match.';
        }
        return $match;
    }

    /**
     * 
     * Checks that the Route `$method` matches the corresponding server value.
     * 
     * @param array $server A copy of $_SERVER.
     * 
     * @return bool True on a match, false if not.
     * 
     */
    protected function isMethodMatch($server) {
        if (isset($this->method)) {
            if (!isset($server['REQUEST_METHOD'])) {
                if (Map::isDebuggable())
                    $this->debug[] = 'Method match requested but REQUEST_METHOD not set.';
                return false;
            }
            if (!in_array($server['REQUEST_METHOD'], $this->method)) {
                if (Map::isDebuggable())
                    $this->debug[] = 'Not a method match.';
                return false;
            }
        }
        return true;
    }

    /**
     * 
     * Checks that the Route `$secure` matches the corresponding server values.
     * 
     * @param array $server A copy of $_SERVER.
     * 
     * @return bool True on a match, false if not.
     * 
     */
    protected function isSecureMatch($server) {
        if ($this->secure !== null) {

            $is_secure = (isset($server['HTTPS']) && $server['HTTPS'] == 'on') || (isset($server['SERVER_PORT']) && $server['SERVER_PORT'] == 443);

            if ($this->secure == true && !$is_secure) {
                if (Map::isDebuggable())
                    $this->debug[] = 'Secure required, but not secure.';
                return false;
            }

            if ($this->secure == false && $is_secure) {
                if (Map::isDebuggable())
                    $this->debug[] = 'Non-secure required, but is secure.';
                return false;
            }
        }
        return true;
    }

    /**
     * 
     * Checks that the custom Route `$is_match` callable returns true, given 
     * the server values.
     * 
     * @param array $server A copy of $_SERVER.
     * 
     * @return bool True on a match, false if not.
     * 
     */
    protected function isCustomMatch($server) {
        if (!$this->is_match) {
            return true;
        }
        $is_match = $this->is_match;
        $matches = new ArrayObject($this->matches);
        $result = $is_match($server, $matches);

        // convert back to array
        $this->matches = $matches->getArrayCopy();

        // did it match?
        if (!$result) {
            if (Map::isDebuggable())
                $this->debug[] = 'Not a custom match.';
        }

        return $result;
    }

}
