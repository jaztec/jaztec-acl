<?php

namespace JaztecAcl\Acl;

interface AclAwareInterface
{

    /**
     * @param \JaztecAcl\Acl\Acl $acl
     */
    public function setAcl(Acl $acl);

    /**
     * @return \JaztecAcl\Acl\Acl $acl
     */
    public function getAcl();
}
