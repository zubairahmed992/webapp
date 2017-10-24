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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Yaml\Parser;

/**
 * Default logout success handler will redirect users to a configured path.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Alexander <iam.asm89@gmail.com>
 */
class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    protected $targetUrl;

    /**
     * @param string    $targetUrl
     */
    public function __construct($targetUrl = '/')
    {
        $this->targetUrl = $targetUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        try{
            if($request->getPathInfo() == '/admin/logout') {
                $yaml = new Parser();
                $conf = $yaml->parse(file_get_contents('../app/config/config.yml'));
                $okta_logout = $conf['twig']['globals']['okta_logout'];
                $okta_app_url = $conf['twig']['globals']['okta_app_url'];
                $this->targetUrl = $okta_logout.urlencode($okta_app_url);
            }
            
            return new RedirectResponse($this->targetUrl);
        }catch (\Exception $e){}
    }
}
