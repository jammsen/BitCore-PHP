<?php
/**
 * 
 * A factory to create Route objects.
 * 
 * 
 */
class RouteFactory
{
    /**
     * 
     * An array of default parameters for Route objects.
     * 
     * @var array
     * 
     */
    protected static $params = array(
        'name'        => null,
        'namespace'   => null,
        'path'        => null,
        'params'      => null,
        'values'      => null,
        'method'      => null,
        'secure'      => false,
        'routable'    => true,
        'is_match'    => null,
        'match_force' => null,
        'generate'    => null,
        'name_prefix' => null,
        'path_prefix' => null,
    );

    /**
     * 
     * Returns a new Route instance.
     * 
     * @param array $params An array of key-value pairs corresponding to the
     * Route parameters.
     * 
     * @return Route
     * 
     */
    public static function newInstance(array $params)
    {
        $params = Matrix::MergeRecursiveDistinct(self::$params, $params);        
        return new Route($params);
    }
}
