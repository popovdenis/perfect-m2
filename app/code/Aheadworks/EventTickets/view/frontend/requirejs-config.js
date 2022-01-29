var config = {
    config: {
        mixins: {
            'mage/validation': {
                'Aheadworks_EventTickets/js/widget/validation-mixin': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'Aheadworks_EventTickets/js/widget/swatch-renderer-mixin': true
            }
        }
    },
    map: {
        '*': {
            awEtConfigurable: 'Aheadworks_EventTickets/js/widget/configurable',
            awEtPriceBox: 'Aheadworks_EventTickets/js/widget/price-box',
            awFullCalendar: 'Aheadworks_EventTickets/js/lib/fullcalendar/core/main',
            awFullCalendarDayGrid: 'Aheadworks_EventTickets/js/lib/fullcalendar/daygrid/main',
            awFullCalendarTimeGrid: 'Aheadworks_EventTickets/js/lib/fullcalendar/timegrid/main',
            awFullCalendarLocales: 'Aheadworks_EventTickets/js/lib/fullcalendar/core/locales-all'
        }
    }
};
