<?php

namespace Experius\Ponumber\Api;

interface PonumberManagementInterface
{
    
    /**
     * Set a ponumber on the cart
     *
     * @param int $cartId.
     * @param string $ponumber
     * @return string
     */
    public function setPonumber($cartId, $ponumber);

    /**
     * Get a ponumber from the cart
     *
     * @param int $cartId.
     * @return string
     */
    public function getPonumber($cartId);
}
