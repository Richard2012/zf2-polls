<?php
namespace Polls;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    // Added in Stage II:
    /*
    This method returns an array of factories that are all merged together by the ModuleManager
    before passing to the ServiceManager.

    The factory for Poll\Model\PollTable uses the ServiceManager to create an PollTableGateway
    to pass to the PollTable.

    We also tell the ServiceManager that an PollTableGateway is created by getting a Zend\Db\Adapter\Adapter
    (also from the ServiceManager) and using it to create a TableGateway object.

    The TableGateway is told to use an Poll object whenever it creates a new result row.
    The TableGateway classes use the prototype pattern for creation of result sets and entities.
    This means that instead of instantiating when required, the system clones a previously instantiated object.
    */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Polls\Model\PollsTable' =>  function($sm) {
                    $tableGateway = $sm->get('PollsTableGateway');
                    $table = new \Polls\Model\PollsTable($tableGateway);
                    return $table;
                },
                'PollsTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new \Polls\Model\Polls());
                    return new TableGateway('polls', $dbAdapter, null, $resultSetPrototype);
                },

                'Polls\Model\AnswersTable' =>  function($sm) {
                    $tableGateway = $sm->get('AnswersTableGateway');
                    $table = new \Polls\Model\AnswersTable($tableGateway);
                    return $table;
                },
                'AnswersTableGateway' => function ($sm) {
                    //$dbAdapter = $sm->get('adapterPoll');
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new \Polls\Model\Answers());
                    return new TableGateway('answers', $dbAdapter, null, $resultSetPrototype);
                },

                'Polls\Model\IplongTable' =>  function($sm) {
                    $tableGateway = $sm->get('IplongTableGateway');
                    $table = new \Polls\Model\IplongTable($tableGateway);
                    return $table;
                },
                'IplongTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new \Polls\Model\Iplong());
                    return new TableGateway('iplong', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

}