<?php

namespace Mautic\SmsBundle\Broadcast;

class BroadcastResult
{
    private int $sentCount = 0;

    private int $failedCount = 0;

    public function process(array $results)
    {
        foreach ($results as $result) {
            if (isset($result['sent']) && true === $result['sent']) {
                $this->sent();
            } else {
                $this->failed();
            }
        }
    }

    public function sent()
    {
        ++$this->sentCount;
    }

    public function failed()
    {
        ++$this->failedCount;
    }

    /**
     * @return int
     */
    public function getSentCount()
    {
        return $this->sentCount;
    }

    /**
     * @return int
     */
    public function getFailedCount()
    {
        return $this->failedCount;
    }
}
