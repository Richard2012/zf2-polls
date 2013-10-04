<?php
namespace Polls\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
//use Polls\Model\Answers;
//use Polls\Model\Polls;
use Polls\Form\newPollForm;

/*
 * TODO: IPv6 not yet processed
 * TODO: Internationalisation (text in different languages)
 * TODO: Check if iframe is in permitted domain (optional)
 *
 * */
class PollsController extends AbstractActionController
{

    protected $pollId;
    protected $pollTable;
    protected $answersTable;
    protected $iplongTable;
    protected $hasVotedMessage;
    protected $hasVoted = false;
    protected $hasJustVoted = false;

    /*
     * These can be modified by developers
     * */
    public $maxCharsInBar       = 20;
    public $showNumberVotes     = true;
    public $replyButton         = 'Vote';
    public $hasVotedText        = 'You have voted already';
    public $seeResultsText      = 'See results without voting';
    public $returnToPollText    = 'Return to poll';
    public $totalVotesText      = 'Total votes';
    public $youHaveVotedText    = 'You have voted already';


    public function __construct($pid=null) {

    }

    public function indexAction()
    {

        return new ViewModel(
            array()
        );
    }

    /*
     * Displays poll form with radio buttons
     * The first screen
     * */
    function pollAction() {

        // Getting the param from the URL
        $event   = $this->getEvent();
        $matches = $event->getRouteMatch();
        $pid = $matches->getParam('id');
        $this->pollId = $pid;

        $pollData = $this->getPollData();
        $view = new ViewModel($pollData);
        $view->setTerminal(true);
        $view->setTemplate('polls/polls/poll');
        return $view;

    }

    /*
     * Called from the poll form
     * Filter input, save result if unique click
     * and either show poll results
     * or repaint with errors
     * */
    public function replyAction()
    {
        $pidFromRoute = $this->getPollIdFromRouteIfEmpty();
        $this->pollId = $pidFromRoute;

        $view = new ViewModel();
        $view->setTerminal(true);

        /* Assuming different id in URL and in POST we give preference to POST */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $myPost = $request->getPost()->toArray();
            if ( (int) $myPost['pollId'] > 0) {
                $this->pollId = (int) $myPost['pollId'];
            }
        }

        if ( ! ($this->pollId > 0)) {
            // Repaint poll form, because pollId was not valid
            $pollData = $this->getPollData();
            $view = new ViewModel($pollData);
            $view->setTerminal(true);
            $view->setTemplate('polls/polls/poll');
            return $view;
        }

        $credentials = array(
            'poll_id'   => (int) $this->pollId,
            'iplong'    => getenv("REMOTE_ADDR"),
            //'iplong'    => (int) ip2long($this->getRandomIp()), // (for testing)
            'extrainfo' => md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['HTTP_ACCEPT_LANGUAGE']),
        );

        $firstTime = ! $this->getIplongTable()->weKnowHim( $credentials);
        if ( $firstTime) {

            $request = $this->getRequest();
            if ($request->isPost()) {
                $postArray = $request->getPost()->toArray();
                $postResult = $this->isValidReply($postArray);
                if ( $postResult === true) {
                    $answers = new \Polls\Model\Answers();
                    $postArray['id'] = $postArray['pollAnswer'];
                    $answers->exchangeArray($postArray);

                    $this->getAnswersTable()->updateVotes($answers);

                    $this->getIplongTable()->rememberVoter( $credentials);

                    /*When forwarding to different action within the same controller,
                    ZF1 will create new instance of controller class.
                    It means that all variables within this controller will be lost.

                    ZF2 does the opposite - if controller object is already created,
                    your data will be intact. */

                    $this->hasJustVoted = true;
                    return $this->forward()
                        ->dispatch('Polls\Controller\Polls', array('action' => 'display'));
                }
            }
        } else {

            $this->hasVoted = true;
            return $this->forward()
                ->dispatch('Polls\Controller\Polls', array('action' => 'display'));
        }

        // no valid answer. Repaint poll form
        $pollData = $this->getPollData();
        $view = new ViewModel($pollData);
        $view->setTerminal(true);
        $view->setTemplate('polls/polls/poll');
        return $view;
    }


    /*
     * Displays poll results
     * Horizontal bars and % of votes are displayed
     * */
    public function displayAction()
    {

        $this->getPollIdFromRouteIfEmpty();
        $pollData                       = $this->getPollResultsData();
        $pollData['hasVoted']           = $this->hasVoted;
        $pollData['hasJustVoted']       = $this->hasJustVoted;
        $pollData['youHaveVotedText']   = $this->youHaveVotedText;
        $pollData['totalVotesText']     = $this->totalVotesText;
        $pollData['returnToPollText']   = $this->returnToPollText;

        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setVariables($pollData);
        $view->setTemplate('polls/polls/display');

        return $view;
    }


    public function getPollIdFromRouteIfEmpty() {

        if (! ($this->pollId > 0)) {
            $event          = $this->getEvent();
            $matches        = $event->getRouteMatch();
            $pid            = $matches->getParam('id');
            $this->pollId   = $pid;
        }

        return $this->pollId;
    }


    public function isValidReply( $aPoll)
    {
        $pid = isset($aPoll['pollId'])     ? (int) $aPoll['pollId']     : 0;
        $aid = isset($aPoll['pollAnswer']) ? (int) $aPoll['pollAnswer'] : 0;
        return ( $pid > 0 && $aid > 0);
    }


    /*
     * The function gathers data necessary for a poll form.
     *
     * It can also be modified to check if the <iframe> has been placed on a permitted server.
     * The name of the permitted sever could be optionally stored in the polls table
     * If polls.server_name is empty , anyone can iframe the poll on his website
     * If you save a server name to the db table then you must use javascript
     * to recover current server or domain name and compare it with the saved value
     * (not done here yet)
     *
     * Only if we want to verify, if IFRAME was placed on the authorised domain:
     * // TODO write the function interface to javascript $this->getDomainFromJavascript()
     * Possibly relevant are:
     * parent.location.href;   parent.document.URL
     * $this->getRequest()->getServer('HTTP_REFERER')
     *  */
    public function getPollData()
    {

        $poll       = $this->getPollsTable()->getPoll( $this->pollId);

        // $this->getRequest()->getHeader('HTTP_REFERER', $defaultValue);

//        $motherDomain = $this->getDomainFromJavascript(); // a function to be written
//        if ( $motherDomain <> $poll->server_name ) {
//            die('iframed on a unathorised server');
//        }

        $answers    = $this->getAnswersTable()->fetchAnswers( $this->pollId);

        $pollData = array(
            'title'             => $poll->name,
            'question'          => $poll->question,
            'radios'            => $answers,
            'action'            => '/polls/reply',
            'replyButton'       => $this->replyButton,
            'pollId'            => $this->pollId,
            'hasVotedText'      => $this->hasVotedText,
            'seeResultsText'    => $this->seeResultsText,
        );

        return $pollData;
    }


    public function getPollResultsData()
    {
        $poll       = $this->getPollsTable()->getPoll( $this->pollId);
        $answers    = $this->getAnswersTable()->fetchAnswers( $this->pollId);

        $results  = array();
        $totalVotes = $maxVotes = $minVotes = $percent100 = 0;
        foreach( $answers as $answer) {
            $results[] = array(
                'answer' => $answer->answer,
                'votes' => $answer->votes,
                'id'    => $answer->id,
            );
            $totalVotes += $answer->votes;
            $maxVotes = max($maxVotes, $answer->votes);
            $maxId    = $answer->id;
            $minVotes = min($minVotes, $answer->votes);
            $minId    = $answer->id;
            if ($maxVotes <= $answer->votes) {
                $maxVotes = $answer->votes;
                $maxId    = $answer->id;
            }
            if ($minVotes >= $answer->votes) {
                $minVotes = $answer->votes;
                $minId    = $answer->id;
            }
        }

        $barCompression = round( $maxVotes / $this->maxCharsInBar);
        $barCompression = max( $barCompression, 1);

        $resultsWithPercentage = array();
        foreach( $results as $result) {

            $percentVotes = ($totalVotes == 0) ? 0 : round((($result['votes'] / $totalVotes)*100));
            $percent100  += $percentVotes;
            $repeatSymbol = max(1, round($result['votes']/$barCompression));
            $repeatSymbol = ($result['votes'] == 0) ? 0 : $repeatSymbol;

            // horizontal bar made of unicode square block character
            // &#x2588; full block smooth, &#x258b; half block, smaller, not smooth
            $votingBar = str_repeat('&#x2588;', $repeatSymbol);

            if ($this->showNumberVotes) {
                $votingResult = $percentVotes . ' (' . $result['votes'] . ' votes)';
            }

            $resultsWithPercentage[$result['id']] = array(
                'answer'        => $result['answer'],
                'votes'         => $result['votes'],
                'votingBar'     => $votingBar,
                'votingResult'  => $votingResult,
                'votingPercent' => $percentVotes,
            );
        }

        // adjust for rounding error
        if ($percent100 > 100) {
            $resultsWithPercentage[$maxId]['votingPercent']--;
        } else if ($percent100 < 100) {
            $resultsWithPercentage[$minId]['votingPercent']++;
        }
        $pollData = array(
            'title'         => $poll->name,
            'question'      => $poll->question,
            'radios'        => $resultsWithPercentage,
            'action'        => '/polls/reply',
            'pollId'        => $this->pollId,
            'totalVotes'    => $totalVotes
        );

        return $pollData;
    }



    function makeRadios( $aQA, $qid) {

        $radioInput = PHP_EOL;
        foreach( $aQA as $answerLine) {
            $name = 'answer' . trim($qid);
            $radioInput .= '<input type="radio" name="'.$name.'" value="'.trim($answerLine->id).'" class="radio_question">
                            <span>' . $answerLine->answer . '</span>' . PHP_EOL;
        }

        return $radioInput;
    }


    public function getPollsTable()
    {

        if (!$this->pollTable) {
            $sm = $this->getServiceLocator();
            $this->pollTable = $sm->get('Polls\Model\PollsTable');
        }
        return $this->pollTable;
    }


    public function getAnswersTable()
    {
        if (!$this->answersTable) {
            $sm = $this->getServiceLocator();
            $this->answersTable = $sm->get('Polls\Model\AnswersTable');
        }
        return $this->answersTable;
    }


    public function getIplongTable()
    {
        if (!$this->iplongTable) {
            $sm = $this->getServiceLocator();
            $this->iplongTable = $sm->get('Polls\Model\IplongTable');
        }
        return $this->iplongTable;
    }


    public function getPollId() {
        return $this->pollId;
    }

    public function setPollId( $pid) {
        $this->pollId = (int) $pid;
    }

    /* just for testing */
    public function getRandomIp( ) {
        $a = rand(1,125);
        $b = rand(125,254);
        $c = rand(1,155);
        $d = rand(130,254);

        return $a . '.' .$b . '.' .$c . '.' .$d;
   }

}