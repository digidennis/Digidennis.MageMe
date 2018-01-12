<?php
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 15-04-16
 * Time: 13:44
 */

namespace Digidennis\MageMe\Security;


class MagentoToken extends \Neos\Flow\Security\Authentication\Token\AbstractToken
{

    /**
     * Updates the authentication credentials, the authentication manager needs to authenticate this token.
     * This could be a username/password from a login controller.
     * This method is called while initializing the security context. By returning TRUE you
     * make sure that the authentication manager will (re-)authenticate the tokens with the current credentials.
     * Note: You should not persist the credentials!
     *
     * @param \Neos\Flow\Mvc\ActionRequest $actionRequest The current request instance
     * @return boolean TRUE if this token needs to be (re-)authenticated
     */
    public function updateCredentials(\Neos\Flow\Mvc\ActionRequest $actionRequest)
    {
        // TODO: Implement updateCredentials() method.
    }
}