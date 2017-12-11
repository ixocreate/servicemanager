<?php
/**
 * kiwi-suite/servicemanager (https://github.com/kiwi-suite/servicemanager)
 *
 * @package kiwi-suite/servicemanager
 * @see https://github.com/kiwi-suite/servicemanager
 * @copyright Copyright (c) 2010 - 2017 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace KiwiSuite\ServiceManager\Autowire;

use Zend\Di\Resolver\ValueInjection;

final class FactoryCode
{
    private $template = <<<'EOD'
<?php
namespace KiwiSuite\GeneratedFactory;

use KiwiSuite\ServiceManager\FactoryInterface;
use KiwiSuite\ServiceManager\ServiceManagerInterface;

final class %s implements FactoryInterface
{
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null)
    {
        %s
        return new \%s(%s);
    }
}

EOD;

    public function generateFactoryCode(string $instanceName, array $resolution): string
    {
        $factoryName = $this->generateFactoryName($instanceName);

        $checkParams = [];
        $constructParams = [];
        foreach ($resolution as $injection) {
            if ($injection instanceof ContainerInjection) {
                $string = '$container->get(\'';
                if ($injection->getContainer() !== null) {
                    $string .= $injection->getContainer() . '\')->get(\'';
                }

                $string .= $injection->getType() . '\')';
                $constructParams[] = $string;
                continue;
            }

            if (!($injection instanceof ValueInjection)) {
                //TODO Exception
            }

            $ifCheck = <<<'EOD'
if (!\is_array($options) || !\array_key_exists('%s', $options)) {
    throw new \KiwiSuite\ServiceManager\Exception\InvalidArgumentException('Invalid option for %s');
}
EOD;
            $checkParams[] = \sprintf($ifCheck, $injection->getParameterName(), $injection->getParameterName());
            $constructParams[] = \sprintf('$options[\'%s\']', $injection->getParameterName());
        }

        return \sprintf(
            $this->template,
            $factoryName,
            \implode("\n", $checkParams),
            $instanceName,
            \implode(",", $constructParams)
        );
    }

    public function generateFactoryName(string $instanceName): string
    {
        return 'Factory' . \md5($instanceName);
    }

    public function generateFactoryFullQualifiedName(string $instanceName): string
    {
        return '\\KiwiSuite\\GeneratedFactory\\' . $this->generateFactoryName($instanceName);
    }
}
