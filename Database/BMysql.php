<?PHP
/**
 * Simple Mysql Wrapper
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): Agent.php
 * @since       0.1.0
 * @package     System/Database/Mysql
 * @category    Database
 */
class BMysql extends BDatabase {
    /**
     * Construct Simple Mysql Connection
     * 
     * @var string $host Mysql Host
     * @var string $username Mysql Username
     * @var string $password Mysql Password
     * @var string $db Mysql DB
     * @var $port $port Port
     * 
     * @see parent::__construct()
     */
    function __construct($host, $username, $password, $db, $port = 3306) {
        
        if ($host == '')
            throw new DatabaseException('no_host');
        if (!$username)
            throw new DatabaseException('no_user');
        if (!$db)
            throw new DatabaseException('no_db');
        if (!$port || !is_numeric($port) || $port < 1 || $port > 65535)
            throw new DatabaseException('wrong_port');

        parent::__construct('mysql:dbname='.$db.';host='.$host.';port='.$port,$username,$password);
    }
}
?>