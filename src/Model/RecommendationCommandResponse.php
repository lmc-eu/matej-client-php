<?php declare(strict_types=1);

namespace Lmc\Matej\Model;

class RecommendationCommandResponse extends CommandResponse
{
    public function getData(): array
    {
        $data = [];
        foreach ($this->data as $key => $val) {
            if (is_string($val)) {
                $val = (object) ['item-id' => $val];
            }
            $data[$key] = $val;
        }

        return $data;
    }
}