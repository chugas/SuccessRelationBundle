<?php

namespace Success\RelationBundle\Event;

use Success\RelationBundle\Model\RelationInterface;
use Symfony\Component\EventDispatcher\Event;

class RelationEvent extends Event {

  private $relation;

  /**
   * Constructs an event.
   *
   * @param \Success\RelationBundle\Model\RelationInterface $relation
   */
  public function __construct(RelationInterface $relation) {
    $this->relation = $relation;
  }

  /**
   * Returns the comment for this event.
   *
   * @return \Success\RelationBundle\Model\RelationInterface
   */
  public function getRelation() {
    return $this->relation;
  }

}
