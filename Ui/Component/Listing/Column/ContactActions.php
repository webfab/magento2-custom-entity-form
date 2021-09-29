<?php

namespace Webfab\CustomProduct\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ContactActions
 */
class ContactActions extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {

                $url = $this->getContext()->getUrl('customproduct/customproduct/delete', ['id' => $item['request_id']]);
                $item[$this->getData('name')]['delete'] = [
                    'href' => $url,
                    'label' => __('Delete'),
                    'confirm' => [
                        'title' => __('Delete ID %1', $item['request_id']),
                        'message' => __('Are you sure you want to delete ID %1 record?', $item['request_id'])
                    ],
                    'hidden' => false,
                ];
            }
        }
        return $dataSource;
    }
}
