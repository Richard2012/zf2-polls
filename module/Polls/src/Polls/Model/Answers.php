<?php
namespace Polls\Model;

use Zend\InputFilter\Factory as InputFactory;     // <-- Added in Stage III
use Zend\InputFilter\InputFilter;                 // <-- Added in Stage III
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Added in Stage III
use Zend\InputFilter\InputFilterInterface;

class Answers implements InputFilterAwareInterface
{
    public $id;
    public $poll_id;
    public $answer;
    public $votes;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id       = (isset($data['id'])) ? $data['id'] : null;
        $this->poll_id  = (isset($data['poll_id'])) ? $data['poll_id'] : null;
        $this->order    = (isset($data['order'])) ? $data['order'] : null;
        $this->answer   = (isset($data['answer'])) ? $data['answer'] : null;
        $this->votes    = (isset($data['votes'])) ? $data['votes'] : null;
    }


    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }


    public function getArrayCopy()
    {
        return get_object_vars($this);
    }


    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'poll_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'votes',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'answer',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}