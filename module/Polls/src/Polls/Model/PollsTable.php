<?php
namespace Polls\Model;

use Zend\Db\TableGateway\TableGateway;

class PollsTable
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

    public function fetchPoll( $id=null)
    {
        $id  = (int) $id;
        $resultSet = $this->tableGateway->select(array('id' => $id));
        return $resultSet;
    }

    public function getPoll($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));

        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }


    public function savePoll(Poll $poll)
    {
        $data = array(
            'name'     => $poll->name,
            'question' => $poll->question,
        );

        $id = (int)$poll->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getPoll($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deletePoll($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}