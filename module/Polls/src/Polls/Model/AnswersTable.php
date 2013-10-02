<?php
namespace Polls\Model;

use Zend\Db\TableGateway\TableGateway;

class AnswersTable
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

    public function fetchAnswers( $poll_id=null)
    {

        $poll_id = (int) $poll_id;
        $resultSet = $this->tableGateway->select(array('poll_id' => $poll_id));
        return $resultSet;
    }

    public function getAnswer($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveAnswer(Answer $answer)
    {
        $data = array(
            'name'     => $answer->name,
            'question' => $answer->question,
        );

        $id = (int)$answer->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAnswer($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }


    public function updateVotes(Answers $answers)
    {
        $data = array(
            'votes' => new \Zend\Db\Sql\Expression("votes + 1"),
        );

        $this->tableGateway->update($data, array('id' => $answers->id));
    }


    public function deleteAnswer($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}