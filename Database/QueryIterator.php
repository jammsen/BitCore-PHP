<?php

/**
 * Simple PDOQuery Iterator
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * 
 * @link        http://stackoverflow.com/questions/159924/how-do-i-loop-through-a-mysql-query-via-pdo-in-php
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): Agent.php
 * @since       0.1.0
 * @package     System/Database/Sqlite
 * @category    Database
 */
class QueryIterator implements Iterator {

    private
            $_stmt,
            $_cache,
            $next;

    public function __construct($stmt) {
        $this->cache = array();
        $this->_stmt = $stmt;
    }

    public function rewind() {
        reset($this->cache);
        $this->next();
    }

    public function valid() {
        return (FALSE !== $this->next);
    }

    public function current() {
        return $this->next[1];
    }

    public function key() {
        return $this->next[0];
    }

    public function next() {
        // Try to get the next element in our data cache.
        $this->next = each($this->cache);

        // Past the end of the data cache
        if (FALSE === $this->next) {
            // Fetch the next row of data
            $row = $this->_stmt->fetch();

            // Fetch successful
            if ($row) {
                $this->cache[] = $row;
            }

            $this->next = each($this->cache);
        }
    }

    public function getStatement() {
        return $this->_stmt;
    }

}
