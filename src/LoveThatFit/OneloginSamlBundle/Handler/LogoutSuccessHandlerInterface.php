<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace LoveThatFit\OneloginSamlBundle\Handler;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Default logout success handler will redirect users to a configured path.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Alexander <iam.asm89@gmail.com>
 */
class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    protected $httpUtils;
    protected $targetUrl;

    /**
     * @param HttpUtils $httpUtils
     * @param string    $targetUrl
     */
    public function __construct(HttpUtils $httpUtils, $targetUrl = '/')
    {
        $this->httpUtils = $httpUtils;

        $this->targetUrl = $targetUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        die('logout success handler');
        /*if($request->getPathInfo() == '/admin/logout') {
            $this->targetUrl = "https://selfiestyler-admin.okta.com/login/admin/signout?fromURI=%2Fapp%2Fselfiestyler_symfonywebapp_1%2Fexk255c4tb2dRHscM1t7%2Fsso%2Fsaml";
        }*/
        return $this->httpUtils->createRedirectResponse($request, $this->targetUrl);
    }
}
