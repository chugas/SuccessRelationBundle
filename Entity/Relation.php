<?php

namespace Success\RelationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Success\RelationBundle\Model\RelationInterface;

/**
 * RelationFollow Doctrine entity.
 *
 * @author Gaston Caldeiro <chugas488@gmail.com>
 */
abstract class Relation implements RelationInterface {

  /**
   * @ORM\Column(name="name", type="string", length=32, nullable=false)
   */
  protected $name;
  
  /**
   * @ORM\Column(name="created_at", type="datetime", nullable=false)
   */
  protected $createdAt;

  protected $entity1;
  protected $entity2;  
  
  /**
   * Constructor.
   * 
   * @param string $name RelationFollow name/key
   */
  public function __construct($name = null) {
    $this->createdAt = new \DateTime();

    if ($name) {
      $this->name = $name;
    }
  }

  /**
   * __toString.
   *
   * @return string
   */
  public function __toString() {
    return sprintf('%s %d', ucfirst($this->getName()), $this->getId());
  }

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Get name
   *
   * @return text
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set name
   *
   * @param text $name
   * @return RelationFollow
   */
  public function setName($name) {
    $this->name = $name;

    return $this;
  }

  public function setCreatedAt(\DateTime $createdAt) {
    $this->createdAt = $createdAt;
  }

  public function getCreatedAt() {
    return $this->createdAt;
  } 
  
  public function getEntity1() {
    return $this->entity1;
  }

  /**
   * Set entity1
   *
   * @param UserInterface $entity1
   * @return Message
   */
  public function setEntity1($entity1 = null) {
    $this->entity1 = $entity1;

    return $this;
  }

  /**
   * Get sentFrom
   *
   * @return UserInterface
   */
  public function getEntity2() {
    return $this->entity2;
  }

  /**
   * Set entity2
   *
   * @param UserInterface $entity2
   * @return Message
   */
  public function setEntity2($entity2 = null) {
    $this->entity2 = $entity2;

    return $this;
  }

}
