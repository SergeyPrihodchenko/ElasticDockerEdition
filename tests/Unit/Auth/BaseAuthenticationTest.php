<?php

declare(strict_types=1);

namespace AvySearch\Tests\Unit\Auth;

use AvySearch\Auth\BaseAuthentication;
use AvySearch\Auth\ElasticAuthenticatorInteface;
use AvySearch\Tests\AbstractTestCase;
use Elastic\Elasticsearch\ClientBuilder;

final class BaseAuthenticationTest extends AbstractTestCase
{
    public function testConstructedProperly(): void
    {
        $host = 'http://localhost:9000';
        $user = 'test_user';
        $password = 'password';

        $BaseAuthenticationObject = new BaseAuthentication($host, $user, $password);

        $SchemeReflection = new \ReflectionClass(BaseAuthentication::class);
        $host_property = $SchemeReflection->getProperty('host')->getValue($BaseAuthenticationObject);
        $user_property = $SchemeReflection->getProperty('user')->getValue($BaseAuthenticationObject);
        $password_property = $SchemeReflection->getProperty('password')->getValue($BaseAuthenticationObject);

        $this->assertSame($host, $host_property);
        $this->assertSame($user, $user_property);
        $this->assertSame($password, $password_property);
    }

    public function testAuthenticateBasic()
    {
        $elasticClientBuilder = $this->createMock(ClientBuilder::class);

        $elasticClientBuilder->expects($this->once())
            ->method('setHosts')
            ->with(['http://test_host:9200'])
            ->willReturn($elasticClientBuilder);

        $elasticClientBuilder->expects($this->once())
            ->method('setBasicAuthentication')
            ->with('test_user', 'test_password')
            ->willReturn($elasticClientBuilder);

        $authenticationObj = new BaseAuthentication('http://test_host:9200', 'test_user', 'test_password');
        $this->assertInstanceOf(ElasticAuthenticatorInteface::class, $authenticationObj);

        $authenticationObj->authenticate($elasticClientBuilder);
    }
}
