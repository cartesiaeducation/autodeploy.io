<?php

namespace AppBundle\Services\Chart;

/**
 * Class Chart;.
 */
class Chart
{
    /**
     * @param int $range
     *
     * @return array
     */
    public function generateDayPeriod($range = 7)
    {
        $days = [];
        for ($i = 0; $i <= 7; ++$i) {
            $dt     = new \DateTime(sprintf('+%d days', $i));
            $days[] = $dt->format('l');
        }

        return array_reverse($days);
    }
}
