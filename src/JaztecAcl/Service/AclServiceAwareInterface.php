<?php

namespace JaztecAcl\Service;

interface AclServiceAwareInterface
{
    /**
     * @param \JaztecAcl\Service\AclService $service
     */
    public function setAclService(AclService $service);

    /**
     * @return @param \JaztecAcl\Service\AclService
     */
    public function getAclService();
}
