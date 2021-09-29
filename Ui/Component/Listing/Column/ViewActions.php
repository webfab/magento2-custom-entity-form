<?php

namespace Webfab\CustomProduct\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class DocumentActions
 *
 * @package Clas\Document\Ui\Component\Listing\Column
 * @author Frédéric TISSOT <ftissot@agencenetdesign.com>
 */
class ViewActions extends Column
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
                $url = $this->getContext()->getUrl('customproduct/customproduct/view', ['request_id' => $item['request_id']]);
                $item[$this->getData('name')]['view'] = [
                    'href' => $url,
                    'label' => __('View'),
                    'hidden' => false,
                ];
            }
        }
        return $dataSource;
    }
}
