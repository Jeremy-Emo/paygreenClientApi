<?php

namespace PaygreenApiClient\Entity;

use DateTime;

class OrderDetails
{
    /**
     * @var int
     */
    private $cycle;

    /**
     * @var int
     */
    private $count;

    /**
     * @var int
     */
    private $day;

    /**
     * @var DateTime
     */
    private $startAt;

    /**
     * @var int
     */
    private $firstAmount;

    /**
     * OrderDetails constructor.
     * @param int $cycle
     * @param int $count
     * @param int $day
     * @param DateTime $startAt
     * @param int $firstAmount
     */
    public function __construct(int $cycle, int $count, int $day, DateTime $startAt, int $firstAmount)
    {
        $this->cycle = $cycle;
        $this->count = $count;
        $this->day = $day;
        $this->startAt = $startAt;
        $this->firstAmount = $firstAmount;
    }

    /**
     * Cast properly this object to array
     * @return array
     */
    public function castToArray() : array
    {
        return [
            'cycle' => $this->cycle,
            'count' => $this->count,
            'day' => $this->day,
            'startAt' => $this->startAt->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z'),
            'firstAmount' => $this->firstAmount,
        ];
    }
}