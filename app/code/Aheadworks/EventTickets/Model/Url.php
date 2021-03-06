<?php
namespace Aheadworks\EventTickets\Model;

use Magento\Framework\UrlInterface;
use Aheadworks\EventTickets\Model\Url\ParamEncryptor;

/**
 * Class Url
 *
 * @package Aheadworks\Rma\Model
 */
class Url
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ParamEncryptor
     */
    private $encryptor;

    /**
     * @param UrlInterface $urlBuilder
     * @param ParamEncryptor $encryptor
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ParamEncryptor $encryptor
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->encryptor = $encryptor;
    }

    /**
     * Retrieve encrypt url
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getEncryptUrl($route, $params = [])
    {
        return $this->urlBuilder->getUrl($route, ['key' => $this->encryptor->encrypt($params)]);
    }
}
