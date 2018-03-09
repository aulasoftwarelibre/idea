<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GenericOAuth2ResourceOwner;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OAuth2SimpleSAMLphpResourceOwner extends GenericOAuth2ResourceOwner
{
    protected $paths = [
        'identifier' => 'sub',
        'nickname' => 'nickname',
        'realname' => 'given_name',
        'email' => 'email',
        'profilepicture' => null,
    ];

    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'authorization_url' => 'https://identidad.uco.es/simplesaml/module.php/oauth2/authorize.php',
            'access_token_url' => 'https://identidad.uco.es/simplesaml/module.php/oauth2/access_token.php',
            'infos_url' => 'https://identidad.uco.es/simplesaml/module.php/oauth2/userinfo.php',

            'use_bearer_authorization' => true,
            'scope' => 'openid profile email',
        ]);
    }

    public function getUserInformation(array $accessToken, array $extraParameters = [])
    {
        // from http://stackoverflow.com/a/28748285/624544
        list(, $jwt) = explode('.', $accessToken['id_token'], 3);

        // if the token was urlencoded, do some fixes to ensure that it is valid base64 encoded
        $jwt = str_replace(['-', '_'], ['+', '/'], $jwt);

        // complete token if needed
        switch (mb_strlen($jwt) % 4) {
            case 0:
                break;

            case 2:
            case 3:
                $jwt .= '=';
                break;

            default:
                throw new \InvalidArgumentException('Invalid base64 format sent back');
        }

        $response = parent::getUserInformation($accessToken, $extraParameters);
        $response->setData(base64_decode($jwt, true));

        return $response;
    }
}
