<?php

namespace AppBundle\Services\Capistrano\Wizard;

/**
 * Class AbstractGenerator.
 */
abstract class AbstractGenerator
{
    /**
     * @param string $varName
     * @param string $value
     * @param bool   $check
     *
     * @return string
     */
    public function generateValue($varName, $value, $check = true)
    {
        $value = trim($value);
        if (!$check) {
            $value = sprintf(', %s', $value);
        } elseif (preg_match('#\{.+\}#', $value, $matches)) {
            $value = sprintf(', -> %s', $value);
        } else {
            $value = sprintf(", '%s'", $value);
        }

        return sprintf('set :%s%s', $varName, $value);
    }
}
