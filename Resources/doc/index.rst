Installation is a quick (I promise!) 2 step process:

1- Setting up the bundle
2- Create your Relation class


1- 
A) Download and install SuccessRelationBundle

B) Enable the bundle

Enable the required bundles in the kernel:

<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...

        new Success\RelationBundle\SuccessRelationBundle(),
    );
}

2-

Setup Doctrine ORM mapping
The ORM implementation does not provide a concrete Relation class for your use, you must create one. This can be done by extending the abstract entities provided by the bundle and creating the appropriate mappings.

For example:

<?php
// src/MyProject/MyBundle/Entity/RelationUserFollow.php

namespace MyProject\MyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Success\RelationBundle\Entity\Relation as BaseRelation;

/**
 * @ORM\Entity
 */
class RelationUser extends BaseRelation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * User one of the relation
     *
     * @var UserInterface
     * @ORM\ManyToOne(targetEntity="Application\Success\UsuarioBundle\Entity\User")
     */
    protected $entity1;

    /**
     * User two of the relation
     *
     * @var UserInterface
     * @ORM\ManyToOne(targetEntity="Application\Success\UsuarioBundle\Entity\User")
     */
    protected $entity2;
}

Configure your application

# app/config/config.yml

success_relation:
    class: Application\Success\UsuarioBundle\Entity\RelationUser
  