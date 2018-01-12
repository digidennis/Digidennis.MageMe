<?php
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 15-04-16
 * Time: 13:50
 */

namespace Digidennis\MageMe\Security;


class MagentoProvider extends \Neos\Flow\Security\Authentication\Provider\AbstractProvider
{

    /**
     * Returns the classnames of the tokens this provider is responsible for.
     *
     * @return array The classname of the token this provider is responsible for
     */
    public function getTokenClassNames()
    {
        return array(\Neos\Flow\Security\Authentication\Token\UsernamePassword::class);
    }

    /**
     * Tries to authenticate the given token. Sets isAuthenticated to TRUE if authentication succeeded.
     *
     * @param \Neos\Flow\Security\Authentication\TokenInterface $authenticationToken The token to be authenticated
     * @return void
     */
    public function authenticate(\Neos\Flow\Security\Authentication\TokenInterface $authenticationToken)
    {
        // TODO: Implement authenticate() method.
    }
}