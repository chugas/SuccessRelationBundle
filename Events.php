<?php

namespace Success\RelationBundle;

final class Events
{
    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the Comment.
     *
     * This event allows you to modify the data in the Comment prior
     * to persisting occuring. The listener receives a
     * FOS\CommentBundle\Event\CommentPersistEvent instance.
     *
     * Persisting of the company can be aborted by calling
     * $event->abortPersist()
     *
     * @var string
     */
    const RELATION_PRE_PERSIST = 'success.relation.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persisted the Comment.
     *
     * This event allows you to notify users or perform other actions
     * that might require the Comment to be persisted before performing
     * those actions. The listener receives a
     * FOS\CommentBundle\Event\CommentEvent instance.
     *
     * @var string
     */
    const RELATION_POST_PERSIST = 'success.relation.post_persist';

    const RELATION_PRE_REMOVE = 'success.relation.pre_remove';
    
    const RELATION_POST_REMOVE = 'success.relation.post_remove';
}
