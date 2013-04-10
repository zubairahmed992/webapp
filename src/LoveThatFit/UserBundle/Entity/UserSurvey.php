<?php

namespace LoveThatFit\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoveThatFit\UserBundle\Entity\UserSurvey
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class UserSurvey
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $question_id
     *
     * @ORM\Column(name="question_id", type="integer", length=22)
     */
    private $question_id;

    /**
     * @var integer $answer_id
     *
     * @ORM\Column(name="answer_id", type="integer", length=22)
     */
    private $answer_id;

    /**
     * @var integer $user_id
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $user_id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set question_id
     *
     * @param integer $questionId
     * @return UserSurvey
     */
    public function setQuestionId($questionId)
    {
        $this->question_id = $questionId;
    
        return $this;
    }

    /**
     * Get question_id
     *
     * @return integer 
     */
    public function getQuestionId()
    {
        return $this->question_id;
    }

    /**
     * Set answer_id
     *
     * @param integer $answerId
     * @return UserSurvey
     */
    public function setAnswerId($answerId)
    {
        $this->answer_id = $answerId;
    
        return $this;
    }

    /**
     * Get answer_id
     *
     * @return integer 
     */
    public function getAnswerId()
    {
        return $this->answer_id;
    }

    /**
     * Set user_id
     *
     * @param integer $userId
     * @return UserSurvey
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
    
        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user_id;
    }
}