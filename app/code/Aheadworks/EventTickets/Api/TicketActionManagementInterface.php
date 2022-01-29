<?php
namespace Aheadworks\EventTickets\Api;

/**
 * Interface ManagerInterface
 *
 * @package Aheadworks\EventTickets\Api
 */
interface TicketActionManagementInterface
{
    /**
     * Perform specified action for the pointed tickets
     * Return array of processed tickets
     * Names of the all basic actions:
     * 'cancel'
     * 'activate'
     * 'checkIn'
     * 'undoCheckIn'
     * 'sendEmail'
     * 'download'
     * Detailed information about actions can be found in the di.xml
     *
     * @param string $actionName
     * @param string[] $ticketsArray
     * @param string[] $additionalData
     * @return \Aheadworks\EventTickets\Api\Data\TicketInterface[]
     * @throws \Exception
     */
    public function doAction($actionName, $ticketsArray, $additionalData = []);
}
