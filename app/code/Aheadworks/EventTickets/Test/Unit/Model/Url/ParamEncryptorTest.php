<?php
namespace Aheadworks\EventTickets\Test\Unit\Model\Url;

use Aheadworks\EventTickets\Model\Url\ParamEncryptor;
use Magento\Framework\Encryption\EncryptorInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class ParamEncryptorTest
 *
 * @package Aheadworks\EventTickets\Test\Unit\Model\Url
 */
class ParamEncryptorTest extends TestCase
{
    /**
     * @var ParamEncryptor
     */
    private $model;

    /**
     * @var EncryptorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $encryptorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp():void
    {
        $objectManager = new ObjectManager($this);
        $this->encryptorMock = $this->getMockForAbstractClass(EncryptorInterface::class);
        $this->model = $objectManager->getObject(
            ParamEncryptor::class,
            [
                'encryptor' => $this->encryptorMock
            ]
        );
    }

    /**
     * Test encrypt method
     */
    public function testEncrypt()
    {
        $params = ['ticket_number' => 'number'];
        $expected = 'MDoyOlhQc2c5TkhsaW9CS0xlTWpnczVwR2ptUHE0QjNMNzB5Ol'
            . 'Fqc0FEMlhoc2ZGOWRXZWRpaGpkY0UvUVBlUnNZekhVMlFtLzV6dHFlcUk9';
        $key = '0:2:XPsg9NHlioBKLeMjgs5pGjmPq4B3L70y:QjsAD2XhsfF9dWedihjdcE/QPeRsYzHU2Qm/5ztqeqI=';

        $this->encryptorMock->expects($this->any())
            ->method('encrypt')
            ->willReturn($key);

        $this->assertEquals($expected, $this->model->encrypt($params));
    }

    /**
     * Test decrypt method
     *
     * @param string $paramKey
     * @param mixed $expected
     * @dataProvider decryptDataProvider
     */
    public function testDecrypt($paramKey, $expected)
    {
        $key = 'MDoyOlhQc2c5TkhsaW9CS0xlTWpnczVwR2ptUHE0QjNMNzB5Ol'
            . 'Fqc0FEMlhoc2ZGOWRXZWRpaGpkY0UvUVBlUnNZekhVMlFtLzV6dHFlcUk9';
        $stringParams = 'ticket_number:number';

        $this->encryptorMock->expects($this->once())
            ->method('decrypt')
            ->willReturn($stringParams);

        $this->assertEquals($expected, $this->model->decrypt($paramKey, $key));
    }

    /**
     * Data provider for decrypt test
     *
     * @return array
     */
    public function decryptDataProvider()
    {
        return [['ticket_number', 'number'], ['key', null]];
    }
}
