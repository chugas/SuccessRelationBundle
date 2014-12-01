<?php

namespace Success\RelationBundle\Twig\Extension;

use Success\RelationBundle\Manager\RelationManager;

/**
 * Relation Twig extension.
 *
 * @author Cédric Dugat <ph3@slynett.com>
 */
class RelationExtension extends \Twig_Extension
{
    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param Manager           $manager Manager service
     */
    public function __construct(RelationManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'relation_exists' => new \Twig_Filter_Method($this, 'relationExists'),
            'relations'       => new \Twig_Filter_Method($this, 'getRelations'),
        );
    }

    /**
     * Returns if relation exists or not.
     *
     * @param object $object1      Object1
     * @param string $relationName Relation name/key
     * @param object $object2      Object2
     *
     * @return boolean
     */
    public function relationExists($object1, $object2)
    {
      return (bool) $this->manager->exists($object1, $object2);
    }

    /**
     * Get relations.
     * 
     * @param object       $object1      Object1
     * @param string       $relationName Relation name/key
     * @param integer|null $limit        Count limit
     * @param string|null  $order        Order name
     * 
     * @return array
     */
    public function getRelations($object1, $limit = null, $order = null)
    {
        return $this->manager->getFollowings($object1, $limit);
    }

    /**
     * Returns extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'success_relation_extension';
    }
}