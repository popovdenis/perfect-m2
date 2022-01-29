<?php
namespace Aheadworks\EventTickets\Ui\Component;

use Magento\Ui\Component\ExportButton as ExportButtonComponent;

/**
 * Class ExportButton
 *
 * @package Aheadworks\EventTickets\Ui\Component
 */
class ExportButton extends ExportButtonComponent
{
    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->prepareAdditionalParams();
        parent::prepare();
    }

    /**
     * Prepare additional params values
     *
     * @return $this
     */
    private function prepareAdditionalParams()
    {
        $config = $this->getData('config');
        if (isset($config['additionalParams'])) {
            $preparedAdditionalParams = [];
            foreach ($config['additionalParams'] as $paramName => $paramValue) {
                if ('*' == $paramValue) {
                    $paramValue = $this->context->getRequestParam($paramName);
                }
                $preparedAdditionalParams[$paramName] = $paramValue;
            }
            $config['additionalParams'] = $preparedAdditionalParams;
            $this->setData('config', $config);
        }
        return $this;
    }
}
