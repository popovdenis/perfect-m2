<?php
namespace Aheadworks\EventTickets\Model\Ticket\Pdf;

use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Document
 *
 * @package Aheadworks\EventTickets\Model\Ticket\Pdf
 */
class Document extends Mpdf
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     * @param array $config
     * @throws MpdfException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        array $config = []
    ) {
        $this->filesystem = $filesystem;
        $config['tempDir'] = $this->filesystem
            ->getDirectoryWrite(DirectoryList::TMP)
            ->getAbsolutePath('aw_et/mpdf');
        parent::__construct($config);
    }

    /**
     * Create document from html
     *
     * @param string $html
     * @return string
     */
    public function createFromHtml($html)
    {
        try {
            $this->WriteHTML($html);
            $pdf = $this->Output('', Destination::STRING_RETURN);
        } catch (MpdfException $e) {
            $pdf = '';
        }

        return $pdf;
    }
}
