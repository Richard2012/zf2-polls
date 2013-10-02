<?php
namespace Polls\Model;

use Zend\Db\TableGateway\TableGateway;

class IplongTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function fetchIplong( $iplong=null)
    {
        $iplong  = (int) $iplong;
        $resultSet = $this->tableGateway->select(array('iplong' => $iplong));
        return $resultSet;
    }

    /*
     * The function receives IPv4 address as $aCredentials['iplong']
     * We let MySql to convert it to integer
     * The result is the same (but processing less complicated)
     * as with PHP ip2long() */
    public function weKnowHim( $aCredentials=array())
    {

        $ipLong = new \Zend\Db\Sql\Expression("INET_ATON(?)", $aCredentials['iplong']);
        $aCredentials['iplong'] = $ipLong;      // now as integer

        $resultSet = $this->tableGateway->select($aCredentials);

        return ! ( $resultSet->count() == 0 ) ;
    }


    /*
     * The function receives IPv4 address as $aCredentials['iplong']
     * We let MySql to convert it to integer
     * The result is the same (but processing less complicated)
     * as with PHP ip2long() */
    public function rememberVoter( $credentials)
    {

        $ipLong = new \Zend\Db\Sql\Expression("INET_ATON(?)", $credentials['iplong']);
        $credentials['iplong'] = $ipLong;  // now as integer

        $this->tableGateway->insert($credentials);
    }


    public function deleteIplong($iplong)
    {
        $this->tableGateway->delete(array('iplong' => $iplong));
    }
}