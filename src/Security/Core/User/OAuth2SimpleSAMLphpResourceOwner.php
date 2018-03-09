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
use Jose\Factory\JWKFactory;
use Jose\Loader;
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
            'jwks_url' => 'https://identidad.uco.es/simplesaml/module.php/oauth2/jwks.php',

            'use_bearer_authorization' => true,
            'scope' => 'openid profile email',
        ]);
    }

    public function getUserInformation(array $accessToken, array $extraParameters = [])
    {
        $jwk_set = JWKFactory::createFromJKU($this->options['jwks_url']);
        $loader = new Loader();
        $jws = $loader->loadAndVerifySignatureUsingKeySet(
            $accessToken['id_token'],
            $jwk_set,
            ['RS256'],
            $signatureIndex
        );
        $jwt = $jws->getPayload();

        $response = parent::getUserInformation($accessToken, $extraParameters);
        $response->setData($jwt);

        return $response;
    }
}
