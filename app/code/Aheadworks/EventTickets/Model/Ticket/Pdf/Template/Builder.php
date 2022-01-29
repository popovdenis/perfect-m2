<?php
namespace Aheadworks\EventTickets\Model\Ticket\Pdf\Template;

use Magento\Framework\Mail\Template\FactoryInterface;

/**
 * Class Builder
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Pdf\Template
 */
class Builder
{
    /**
     * @var string
     */
    private $templateIdentifier;

    /**
     * @var FactoryInterface
     */
    private $templateFactory;

    /**
     * @var array
     */
    private $templateVars;

    /**
     * @var array
     */
    private $templateOptions;

    /**
     * @param FactoryInterface $templateFactory
     */
    public function __construct(
        FactoryInterface $templateFactory
    ) {
        $this->templateFactory = $templateFactory;
    }

    /**
     * Set template identifier
     *
     * @param string $templateIdentifier
     * @return $this
     */
    public function setTemplateIdentifier($templateIdentifier)
    {
        $this->templateIdentifier = $templateIdentifier;
        return $this;
    }

    /**
     * Set template vars
     *
     * @param array $templateVars
     * @return $this
     */
    public function setTemplateVars($templateVars)
    {
        $this->templateVars = $templateVars;
        return $this;
    }

    /**
     * Set template options
     *
     * @param array $templateOptions
     * @return $this
     */
    public function setTemplateOptions($templateOptions)
    {
        $this->templateOptions = $templateOptions;
        return $this;
    }

    /**
     * Prepare message
     *
     * @return string
     */
    public function build()
    {
        $template = $this->getTemplate();
        $body = $template->processTemplate();

        return $body;
    }

    /**
     * Get template
     *
     * @return \Magento\Framework\Mail\TemplateInterface
     */
    private function getTemplate()
    {
        return $this->templateFactory->get($this->templateIdentifier)
            ->setVars($this->templateVars)
            ->setOptions($this->templateOptions);
    }
}
