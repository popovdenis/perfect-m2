<?php
namespace Aheadworks\EventTickets\Model\Ticket\Email;

use Magento\Framework\UrlInterface;

/**
 * Class UrlBuilder
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Email
 */
class UrlBuilder
{
    /**
     * @var UrlInterface
     */
    private $frontendUrlBuilder;

    /**
     * @param UrlInterface $frontendUrlBuilder
     */
    public function __construct(
        UrlInterface $frontendUrlBuilder
    ) {
        $this->frontendUrlBuilder = $frontendUrlBuilder;
    }

    /**
     * Get action url
     *
     * @param string $routePath
     * @param string $scope
     * @param array $params
     * @return string
     */
    public function getUrl($routePath, $scope, $params)
    {
        $this->frontendUrlBuilder->setScope($scope);
        $href = $this->frontendUrlBuilder->getUrl(
            $routePath,
            $params
        );

        return $href;
    }
}
