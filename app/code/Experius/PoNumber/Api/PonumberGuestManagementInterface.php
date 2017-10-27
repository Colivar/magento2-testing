<?php

namespace Experius\Ponumber\Api;

interface PonumberGuestManagementInterface
{
    
    /**
     * Set a ponumber on the cart
     *
     * @param string $cartId.
     * @param string $ponumber
     * @return string
     */
    public function setPonumberGuest($cartId, $ponumber);

    /**
     * Get a ponumber from the cart
     *
     * @param int $cartId.
     * @return string
     */
    public function getPonumber($cartId);
}
