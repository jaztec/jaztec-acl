<?php

namespace Jaztec\Acl;

interface AclAwareInterface {

    /**
     * @param \Jaztec\Acl\Acl $acl
     */
    public function setAcl(Acl $acl);

    /**
     * @return \Jaztec\Acl\Acl $acl
     */
    public function getAcl();
}