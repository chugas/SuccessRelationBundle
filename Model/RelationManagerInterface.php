<?php

namespace Success\RelationBundle\Model;

use Success\RelationBundle\Model\RelationInterface;

/**
 * RelationManager interface.
 *
 * @author Cédric Dugat <ph3@slynett.com>
 */
interface RelationManagerInterface
{
    /**
     * Get specific relation between 2 objects.
     *
     * @param RelationInterface $relationShip Relationship
     *
     * @return RelationInterface|null
     */
    public function getRelation($entity1, $entity2);

    /**
     * Add a new relation.
     *
     * @param RelationInterface $relation Relation
     *
     * @return RelationInterface
     */
    public function addRelation(RelationInterface $relation);

}
