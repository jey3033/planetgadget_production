<?php
/**
 * Kemana_Banner
 * @author Hasitha Anuruddha <hhanuruddha@kemana.com>
 * @see README.md
 *
 */
namespace Kemana\Banner\Setup\Patch\Data;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Filesystem;
use Kemana\Banner\Model\Config\Source\Template;
use Psr\Log\LoggerInterface;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Module\Dir;

/**
 * Class CopyDemoImages
 */
class CopyDemoImages implements DataPatchInterface, PatchVersionInterface
{
    /**
     * Const for current module name
     */
    const CURRENT_MODULE_NAME = 'Kemana_Banner';
    
    /**
     * Const for demo banner path
     */
    const BANNER_URL_PATH = 'kemana/bannerslider/banner/demo/';

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var Template
     */
    protected $template;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Reader
     */
    protected $moduleReader;

    /**
     * CopyDemoImages constructor.
     * @param Filesystem $filesystem
     * @param Template $template
     * @param LoggerInterface $logger
     * @param Reader $moduleReader
     */
    public function __construct(
        Filesystem $filesystem,
        Template $template,
        LoggerInterface $logger,
        Reader $moduleReader
    ) {
        $this->fileSystem = $filesystem;
        $this->template = $template;
        $this->logger = $logger;
        $this->moduleReader = $moduleReader;
    }

    /**
     * @return DataPatchInterface|void
     */
    public function apply()
    {
        try {
            $mediaDirectory = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
            $mediaDirectory->create(self::BANNER_URL_PATH);
            $demos = $this->template->toOptionArray();
            foreach ($demos as $demo) {
                $targetPath = $mediaDirectory->getAbsolutePath(self::BANNER_URL_PATH . $demo['value']);
                $DS = DIRECTORY_SEPARATOR;
                $viewDir = $this->moduleReader->getModuleDir(
                    Dir::MODULE_VIEW_DIR,
                    self::CURRENT_MODULE_NAME
                );
                $oriPath = $viewDir . $DS . 'adminhtml' . $DS . 'web' . $DS . 'images' . $DS . $demo['value'];
                $mediaDirectory->getDriver()->copy($oriPath, $targetPath);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '2.41.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
