<?php

namespace Success\RelationBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Success\RelationBundle\Model\RelationInterface;
use Success\RelationBundle\Model\RelationManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Success\RelationBundle\Event\RelationEvent;
use Success\RelationBundle\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/*
  // CREAR RELACION
  $manager = $this->container->get('success.relation_follow.relation_manager');
  $manager->create($userLogged, $userContact, 'follow');


  // BORRAR RELACION
  $manager = $this->container->get('success.relation_follow.relation_manager');
  $manager->remove($userLogged, $userContact, 'follow');

 */

/**
 * RelationManager.
 *
 * @uses RelationManagerInterface
 * @author Gaston Caldeiro <chugas488@gmail.com>
 */
class RelationManager implements RelationManagerInterface {

  /**
   * @var ObjectManager
   */
  protected $em;
  protected $class;
  protected $relation;
  protected $dispatcher;

  /**
   * @param EntityManager $em         Entity manager service
   * @param string        $repository Repository name
   */
  public function __construct(EntityManager $em, $class, EventDispatcherInterface $dispatcher) {
    $this->em = $em;
    $this->class = $class;
    $this->dispatcher = $dispatcher;
  }

  /**
   * Returns if relation exists or not.
   *
   * @return boolean
   */
  public function exists($entity1, $entity2) {
    $this->relation = $this->getRelation($entity1, $entity2);

    return !is_null($this->relation);
  }

  /**
   * Create relation.
   *
   * @return RelationInterface|null
   */
  public function create($entity1, $entity2, $name = 'follow') {
    if ($this->exists($entity1, $entity2)) {
      return true;
    }

    $class = $this->class;
    $relation = new $class();
    $relation->setName($name);
    $relation->setEntity1($entity1);
    $relation->setEntity2($entity2);

    return $this->addRelation($relation);
  }

  /**
   * Create relation.
   *
   * @return RelationInterface|null
   */
  public function remove($entity1, $entity2) {
    if (!$this->exists($entity1, $entity2)) {
      return false;
    }

    return $this->removeRelation($this->relation);
  }

  /**
   * {@inheritdoc}
   */
  public function addRelation(RelationInterface $relation) {
    $event = new RelationEvent($relation);
    $this->dispatcher->dispatch(Events::RELATION_PRE_PERSIST, $event);
    
    $this->em->persist($relation);
    $this->em->flush();

    return $relation;
  }

  public function removeRelation(RelationInterface $relation) {
    // Notificar Evento de borrado
    $event = new RelationEvent($relation);
    $this->dispatcher->dispatch(Events::RELATION_PRE_REMOVE, $event);

    $this->em->remove($relation);
    $this->em->flush();

    return true;
  }

  /**
   * {@inheritdoc}
   */
  public function getRelation($entity1, $entity2) {
    $q = $this->__getRepository()
            ->createQueryBuilder('r')
            ->where('r.entity1 = :entity1')
            ->andWhere('r.entity2 = :entity2');

    $q->setParameters(array(
        'entity1' => $entity1,
        'entity2' => $entity2
    ));

    return $q->getQuery()->getOneOrNullResult();
  }

  public function getFollowers($entity, $limit = null) {
    $q = $this->getFollowersQuery($entity);

    if (!is_null($limit)) {
      $q->setMaxResults($limit);
    }

    $relations = $q->getResult();

    $collection = new ArrayCollection();
    foreach ($relations as $relation) {
      $collection->add($relation->getEntity2());
    }

    return $collection;
  }

  public function getFollowings($entity, $limit = null) {
    $q = $this->getFollowingsQuery($entity);

    if (!is_null($limit)) {
      $q->setMaxResults($limit);
    }

    $relations = $q->getResult();

    $collection = new ArrayCollection();
    foreach ($relations as $relation) {
      $collection->add($relation->getEntity2());
    }

    return $collection;
  }

  public function getFollowersQuery($entity) {
    $q = $this->__getRepository()
            ->createQueryBuilder('r')
            ->select('r', 'e1')
            ->innerJoin('r.entity1', 'e1')
            ->where('r.entity2 = :entity2')
            ->orderBy('r.createdAt', 'DESC');

    $q->setParameter('entity2', $entity);

    return $q->getQuery();
  }

  public function getFollowingsQuery($entity) {
    $q = $this->__getRepository()
            ->createQueryBuilder('r')
            ->select('r', 'e2')
            ->innerJoin('r.entity2', 'e2')
            ->where('r.entity1 = :entity1')
            ->orderBy('r.createdAt', 'DESC');

    $q->setParameter('entity1', $entity);

    return $q->getQuery();
  }

  /**
   * Get entity repository.
   *
   * @return EntityRepository
   */
  private function __getRepository() {
    return $this->em->getRepository($this->class);
  }

}
