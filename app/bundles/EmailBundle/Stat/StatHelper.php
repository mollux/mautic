<?php

namespace Mautic\EmailBundle\Stat;

use Mautic\EmailBundle\Entity\Stat;
use Mautic\EmailBundle\Entity\StatRepository;
use Mautic\EmailBundle\Stat\Exception\StatNotFoundException;

class StatHelper
{
    /**
     * Just store email ID and lead ID to avoid doctrine RAM issues with entities.
     *
     * @var Reference[]
     */
    private array $stats = [];

    private array $deleteUs = [];

    /**
     * StatHelper constructor.
     */
    public function __construct(private StatRepository $repo)
    {
    }

    /**
     * @param $emailAddress
     */
    public function storeStat(Stat $stat, $emailAddress)
    {
        $this->repo->saveEntity($stat);

        // to avoid Doctrine RAM issues, we're just going to hold onto ID references
        $this->stats[$emailAddress] = new Reference($stat);

        // clear stat from doctrine memory
        $this->repo->clear();
    }

    public function deletePending()
    {
        if (count($this->deleteUs)) {
            $this->repo->deleteStats($this->deleteUs);
        }
    }

    public function markForDeletion(Reference $stat)
    {
        $this->deleteUs[] = $stat->getStatId();
    }

    /**
     * @param $emailAddress
     *
     * @return Reference
     *
     * @throws StatNotFoundException
     */
    public function getStat($emailAddress)
    {
        if (!isset($this->stats[$emailAddress])) {
            throw new StatNotFoundException();
        }

        return $this->stats[$emailAddress];
    }

    public function reset()
    {
        $this->deleteUs = [];
        $this->stats    = [];
    }
}
