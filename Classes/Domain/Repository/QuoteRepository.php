<?php
namespace Digidennis\MageMe\Domain\Repository;

/*
 * This file is part of the Digidennis.MageMe package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;
use Neos\Flow\Persistence\QueryInterface;
use Digidennis\MageMe\Domain\Model\Quote;


/**
 * @Flow\Scope("singleton")
 */
class QuoteRepository extends Repository
{

    /**
     * Finds the previous of the given post
     *
     * @param string $email
     * @return Quote
     */
    public function findNewestUnPickedByEmail($email) {
        $query = $this->createQuery();
        return $query->matching(
                $query->logicalAnd([
                    $query->equals('email', $email),
                    $query->equals('picked', 0)
                ])
            )
            ->setOrderings(array('date' => QueryInterface::ORDER_DESCENDING))
            ->execute()
            ->getFirst();
    }
}
