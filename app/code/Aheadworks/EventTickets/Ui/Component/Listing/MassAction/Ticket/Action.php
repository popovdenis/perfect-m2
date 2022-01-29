<?php
namespace Aheadworks\EventTickets\Ui\Component\Listing\MassAction\Ticket;

use Magento\Ui\Component\Control\Action as ActionControl;

/**
 * Class Action
 *
 * @package Aheadworks\EventTickets\Ui\Component\Listing\MassAction\Ticket
 */
class Action extends ActionControl
{
    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->prepareActionUrl();
        parent::prepare();
    }

    /**
     * Prepare url params values
     *
     * @return $this
     */
    private function prepareActionUrl()
    {
        $config = $this->getData('config');
        if (isset($config['url_route']) && isset($config['additionalParams'])) {
            $additionalParams = $this->getPreparedAdditionalParams($config['additionalParams']);
            $config['url'] = $this->getActionUrl($config['url_route'], $additionalParams);
            $this->setData('config', (array)$config);
        }
        return $this;
    }

    /**
     * Retrieve prepared params for action url
     *
     * @param array $paramsData
     * @return array
     */
    private function getPreparedAdditionalParams($paramsData)
    {
        $preparedParams = [];
        if (is_array($paramsData)) {
            foreach ($paramsData as $paramName => $paramValue) {
                if ('*' == $paramValue) {
                    $paramValue = $this->getContext()->getRequestParam($paramName);
                }
                $preparedParams[$paramName] = $paramValue;
            }
        }
        return $preparedParams;
    }

    /**
     * Retrieve action url
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    private function getActionUrl($route, $params)
    {
        return $this->getContext()->getUrl($route, $params);
    }
}
