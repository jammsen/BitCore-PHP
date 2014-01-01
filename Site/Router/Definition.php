<?php

/**
 * Represents a route definition, either a single route or a set of attached
 * routes.
 *
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * 
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): Definition.php
 * @package     Site/Router/Definition
 * @category    Router
 */
class Definition
{
    /**
     * 
     * The type of definition, typically 'single' or 'attach'.
     * 
     * @var string
     * 
     */
    protected $type;
    
    /**
     * 
     * The spec for the definition.
     * 
     * @var array|callable
     * 
     */
    protected $spec;
    
    /**
     * 
     * For 'attach' definitions, the prefix for all attached route paths.
     * 
     * @var string
     * 
     */
    protected $path_prefix;
    
    /**
     * 
     * Constructor.
     * 
     * @param string $type The definition type.
     * 
     * @param array|callable $spec The definition spec.
     * 
     * @param string $path_prefix The prefix for 'attach' paths.
     * 
     */
    public function __construct($type, $spec, $path_prefix = null)
    {
        $this->type        = $type;
        $this->spec        = $spec;
        $this->path_prefix = $path_prefix;
    }
    
    /**
     * 
     * Returns the definition type.
     * 
     * @return string
     * 
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * 
     * Returns the definition spec, converting callable along the way, and
     * setting the path_prefix on 'attach' definitions.
     * 
     * @return array
     * 
     */
    public function getSpec()
    {
        if (is_callable($this->spec)) {
            $this->spec = call_user_func($this->spec);
        }
        
        if ($this->type == Map::MAP_ROUTE_ATTACH) {
            $this->spec['path_prefix'] = $this->path_prefix;
        }
        
        return $this->spec;
    }
}
