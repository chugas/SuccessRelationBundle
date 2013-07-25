<?php

namespace Success\RelationBundle\Model;

/**
 * Relation inteface.
 *
 * @author Cédric Dugat <ph3@slynett.com>
 */
interface RelationInterface
{
    /**
     * Get ID.
     *
     * @return integer
     */
    public function getId();

    /**
     * Set name value.
     *
     * @param string $name Name
     */
    public function setName($name);

    /**
     * Get name value.
     *
     * @return string
     */
    public function getName();

    /**
     * Set entity object1 value.
     *
     * @param string $entity Object value
     */
    public function setEntity1($entity);

    /**
     * Get entity object1 value.
     *
     * @return string
     */
    public function getEntity1();

    /**
     * Set entity object2 value.
     *
     * @param string $entity Object value
     */
    public function setEntity2($entity);

    /**
     * Get entity object2 value.
     *
     * @return string
     */
    public function getEntity2();

    /**
      * Set createdAt value.
      *
      * @param \DateTime $createdAt CreatedAt value
      */
    public function setCreatedAt(\DateTime $createdAt);

    /**
      * Get createdAt value.
      *
      * @return \DateTime
      *
      */
    public function getCreatedAt();
}
