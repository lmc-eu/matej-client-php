<?php declare(strict_types=1);

namespace Lmc\Matej\RequestBuilder;

use Fig\Http\Message\RequestMethodInterface;
use Lmc\Matej\Exception\LogicException;
use Lmc\Matej\Model\Assertion;
use Lmc\Matej\Model\Command\ItemPropertySetup;
use Lmc\Matej\Model\Request;

class ItemPropertiesSetupRequestBuilder extends AbstractRequestBuilder
{
    protected const ENDPOINT_PATH = '/item-properties';

    /** @var ItemPropertySetup[] */
    protected $commands = [];
    /** @var bool */
    protected $shouldDelete = false;

    /**
     * @param bool $shouldDelete Should the request delete item properties instead of creating new?
     */
    public function __construct(bool $shouldDelete = false)
    {
        $this->shouldDelete = $shouldDelete;
    }

    public function addProperty(ItemPropertySetup $itemPropertySetup): self
    {
        $this->commands[] = $itemPropertySetup;

        return $this;
    }

    /**
     * @param ItemPropertySetup[] $itemPropertiesSetup
     * @return self
     */
    public function addProperties(array $itemPropertiesSetup): self
    {
        foreach ($itemPropertiesSetup as $itemPropertySetup) {
            $this->addProperty($itemPropertySetup);
        }

        return $this;
    }

    public function build(): Request
    {
        if (empty($this->commands)) {
            throw new LogicException(
                'At least one ItemPropertySetup command must be added to the builder before sending the request'
            );
        }
        Assertion::batchSize($this->commands);

        $method = $this->shouldDelete ? RequestMethodInterface::METHOD_DELETE : RequestMethodInterface::METHOD_PUT;

        return new Request(self::ENDPOINT_PATH, $method, $this->commands, $this->requestId);
    }
}
