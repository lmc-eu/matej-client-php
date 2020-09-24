<?php declare(strict_types=1);

namespace Lmc\Matej\Model;

class RecommendationCommandResponse extends CommandResponse
{
    /**
     * Return recommendations data.
     */
    public function getData(): array
    {
        $data = [];
        /* If Matej returns flat structure response `['id1', 'id2']`, convert it
           to complex structure `[['item_id': 'id1'], ['item_id': 'id2']]`. This
           ensures `RecommendationCommandResponse::getData` returns data in
           consistent format. */
        foreach ($this->data as $key => $val) {
            if (is_string($val)) {
                $val = (object) ['item-id' => $val];
            }
            $data[$key] = $val;
        }

        return $data;
    }
}
