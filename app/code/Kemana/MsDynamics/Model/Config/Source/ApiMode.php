<?php

namespace Kemana\MsDynamics\Model\Config\Source;

class ApiMode implements \Magento\Framework\Option\ArrayInterface
{   
    public function toOptionArray()
    {
        return ['Sandbox','Live'];
    }
}