<?php
namespace Aheadworks\EventTickets\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Backend\Model\Session;
use Magento\Framework\App\ActionFlag;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Action\Action;

/**
 * Class ControllerActionPredispatchObserver
 *
 * @package Aheadworks\EventTickets\Observer
 */
class ControllerActionPredispatchObserver implements ObserverInterface
{
    /**
     * Key for the parameter in the request
     */
    const AW_ET_PAGE_TO_RETURN_PARAM_KEY = 'awEtPageToReturn';

    /**
     * @var Session
     */
    private $session;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param Session $session
     * @param ActionFlag $actionFlag
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Session $session,
        ActionFlag $actionFlag,
        UrlInterface $urlBuilder
    ) {
        $this->session = $session;
        $this->actionFlag = $actionFlag;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\App\Action\Action $controller */
        $controller = $observer->getEvent()->getControllerAction();
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $observer->getEvent()->getRequest();
        $actionName = $request->getFullActionName();

        if (is_object($controller) && is_object($request)) {
            switch ($actionName) {
                case 'catalog_product_index':
                    $this->session->setIsNeedToRedirectToProductGrid(true);
                    if ($controller->getRequest()->getParam('menu', false)) {
                        $this->session->setAwEtPageToReturn(false);
                    } else {
                        if ($awEtPageToReturn = $this->session->getAwEtPageToReturn()) {
                            $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
                            $this->actionFlag->set('', Action::FLAG_NO_POST_DISPATCH, true);
                            $this->session->setAwEtPageToReturn(false);

                            $urlToReturn = $this->getUrlToReturn($awEtPageToReturn);

                            $controller->getResponse()->setRedirect($urlToReturn);
                        }
                    }
                    break;
                case 'catalog_product_edit':
                case 'catalog_product_new':
                    if ($this->session->getIsNeedToRedirectToProductGrid()) {
                        $this->session->setAwEtPageToReturn(false);
                    }
                    break;
            }
        }

        return $this;
    }

    /**
     * Retrieve url to return according to the parameter from the session
     *
     * @param string $awEtPageToReturn
     * @return string
     */
    private function getUrlToReturn($awEtPageToReturn)
    {
        //@todo set valid tickets grid url
        $routePathToReturn = 'aw_event_tickets/ticket/index';
        if ($awEtPageToReturn === 'aw_et_event_index') {
            $routePathToReturn = 'aw_event_tickets/event/index';
        }
        if ($awEtPageToReturn === 'aw_et_recurring_event_index') {
            $routePathToReturn = 'aw_event_tickets/recurring_event/index';
        }
        return $this->urlBuilder->getUrl($routePathToReturn);
    }
}
