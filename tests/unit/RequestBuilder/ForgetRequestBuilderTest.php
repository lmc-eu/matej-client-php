<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\DomainException;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Http\RequestManager;
use Lmc\Matej\Model\Command\UserForget;
use Lmc\Matej\Model\Request;
use Lmc\Matej\Model\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lmc\Matej\RequestBuilder\AbstractRequestBuilder
 * @covers \Lmc\Matej\RequestBuilder\ForgetRequestBuilder
 */
class ForgetRequestBuilderTest extends TestCase
{
    /** @test */
    public function shouldBuildRequestWithCommands(): void
    {
        $builder = new ForgetRequestBuilder();

        $anonymizeUserA = UserForget::anonymize('user-anonymize-a');
        $anonymizeUserB = UserForget::anonymize('user-anonymize-b');
        $deleteUserA = UserForget::delete('user-delete-a');
        $deleteUserB = UserForget::delete('user-delete-b');

        $builder->addUser($anonymizeUserA);
        $builder->addUser($deleteUserA);

        $builder->addUsers([$anonymizeUserB, $deleteUserB]);

        $builder->setRequestId('custom-request-id-foo');

        $request = $builder->build();

        $this->assertSame(RequestMethodInterface::METHOD_POST, $request->getMethod());
        $this->assertSame('/forget', $request->getPath());

        $requestData = $request->getData();
        $this->assertCount(4, $requestData);
        $this->assertContains($anonymizeUserA, $requestData);
        $this->assertContains($anonymizeUserB, $requestData);
        $this->assertContains($deleteUserA, $requestData);
        $this->assertContains($deleteUserB, $requestData);

        $this->assertSame('custom-request-id-foo', $request->getRequestId());
    }

    /** @test */
    public function shouldThrowExceptionWhenBuildingEmptyCommands(): void
    {
        $builder = new ForgetRequestBuilder();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'At least one UserForget command must be added to the builder before sending the request'
        );
        $builder->build();
    }

    /** @test */
    public function shouldThrowExceptionWhenBatchSizeIsTooBig(): void
    {
        $builder = new ForgetRequestBuilder();

        for ($i = 0; $i < 501; $i++) {
            $builder->addUser(UserForget::delete('userid-delete-' . $i));
            $builder->addUser(UserForget::anonymize('userid-anonymize-' . $i));
        }

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Request contains 1002 commands, but at most 1000 is allowed in one request.');
        $builder->build();
    }

    /** @test */
    public function shouldThrowExceptionWhenSendingCommandsWithoutRequestManager(): void
    {
        $builder = new ForgetRequestBuilder();

        $builder->addUser(UserForget::delete('user-delete-a'));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Instance of RequestManager must be set to request builder');
        $builder->send();
    }

    /** @test */
    public function shouldSendRequestViaRequestManager(): void
    {
        $requestManagerMock = $this->createMock(RequestManager::class);
        $requestManagerMock->expects($this->once())
            ->method('sendRequest')
            ->with($this->isInstanceOf(Request::class))
            ->willReturn(new Response(0, 0, 0, 0));

        $builder = new ForgetRequestBuilder();
        $builder->setRequestManager($requestManagerMock);

        $builder->addUser(UserForget::delete('user-delete-a'));

        $builder->send();
    }
}
