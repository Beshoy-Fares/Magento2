<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Product\Option\Type\File;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Catalog\Model\Product\Exception as ProductException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Framework\App\ObjectManager;

/**
 * Validator class. Represents logic for validation file given from product option
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ValidatorFile extends Validator
{
    /**
     * Relative path for main destination folder
     *
     * @var string
     */
    protected $path = 'custom_options';

    /**
     * Relative path for quote folder
     *
     * @var string
     */
    protected $quotePath = 'custom_options/quote';

    /**
     * Relative path for order folder
     *
     * @var string
     */
    protected $orderPath = 'custom_options/order';

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\HTTP\Adapter\FileTransferFactory
     */
    protected $httpFactory;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var \Magento\Framework\Validator\File\IsImage
     */
    protected $isImageValidator;

    /**
     * @var Random
     */
    private $random;

    /**
<<<<<<< HEAD
=======
     * Constructor method
     *
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\File\Size $fileSize
     * @param \Magento\Framework\HTTP\Adapter\FileTransferFactory $httpFactory
     * @param \Magento\Framework\Validator\File\IsImage $isImageValidator
     * @param Random|null $random
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Size $fileSize,
        \Magento\Framework\HTTP\Adapter\FileTransferFactory $httpFactory,
        \Magento\Framework\Validator\File\IsImage $isImageValidator,
        Random $random = null
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->filesystem = $filesystem;
        $this->httpFactory = $httpFactory;
        $this->isImageValidator = $isImageValidator;
        $this->random = $random
            ?? ObjectManager::getInstance()->get(Random::class);
        parent::__construct($scopeConfig, $filesystem, $fileSize);
    }

    /**
     * Setter method for the product
     *
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Validation method
     *
     * @param \Magento\Framework\DataObject $processingParams
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return array
     * @throws LocalizedException
     * @throws ProductException
     * @throws \Exception
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Validator\Exception
     * @throws \Zend_File_Transfer_Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function validate($processingParams, $option)
    {
        $upload = $this->httpFactory->create();
        $file = $processingParams->getFilesPrefix() . 'options_' . $option->getId() . '_file';
        try {
            $runValidation = $option->getIsRequire() || $upload->isUploaded($file);
            if (!$runValidation) {
                throw new \Magento\Framework\Validator\Exception(
                    __(
                        'The validation failed. '
                        . 'Make sure the required options are entered and the file is uploaded, then try again.'
                    )
                );
            }

            $fileInfo = $upload->getFileInfo($file)[$file];
            $fileInfo['title'] = $fileInfo['name'];
        } catch (\Magento\Framework\Validator\Exception $e) {
            throw $e;
        } catch (\Exception $e) {
            // when file exceeds the upload_max_filesize, $_FILES is empty
            if ($this->validateContentLength()) {
                $value = $this->fileSize->getMaxFileSizeInMb();
                throw new LocalizedException(
                    __(
                        "The file was too big and couldn't be uploaded. "
                        . "Use a file smaller than %1 MBs and try to upload again.",
                        $value
                    )
                );
            } else {
                throw new ProductException(__("The required option wasn't entered. Enter the option and try again."));
            }
        }

        /**
         * Option Validations
         */
        $upload = $this->buildImageValidator($upload, $option);

        /**
         * Upload process
         */
        $this->initFilesystem();
        $userValue = [];

        if ($upload->isUploaded($file) && $upload->isValid($file)) {
            $fileName = \Magento\MediaStorage\Model\File\Uploader::getCorrectFileName($fileInfo['name']);
            $dispersion = \Magento\MediaStorage\Model\File\Uploader::getDispersionPath($fileName);

            $filePath = $dispersion;

            $tmpDirectory = $this->filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
            $fileHash = md5($tmpDirectory->readFile($tmpDirectory->getRelativePath($fileInfo['tmp_name'])));
            $fileRandomName = $this->random->getRandomString(32);
            $filePath .= '/' .$fileRandomName;
            $fileFullPath = $this->mediaDirectory->getAbsolutePath($this->quotePath . $filePath);

            $upload->addFilter(new \Zend_Filter_File_Rename(['target' => $fileFullPath, 'overwrite' => true]));

            if ($this->product !== null) {
                $this->product->getTypeInstance()->addFileQueue(
                    [
                        'operation' => 'receive_uploaded_file',
                        'src_name' => $file,
                        'dst_name' => $fileFullPath,
                        'uploader' => $upload,
                        'option' => $this,
                    ]
                );
            }

            $_width = 0;
            $_height = 0;

            if ($tmpDirectory->isReadable($tmpDirectory->getRelativePath($fileInfo['tmp_name']))) {
                if (filesize($fileInfo['tmp_name'])) {
                    if ($this->isImageValidator->isValid($fileInfo['tmp_name'])) {
                        $imageSize = getimagesize($fileInfo['tmp_name']);
                    }
                } else {
                    throw new LocalizedException(__('The file is empty. Select another file and try again.'));
                }

                if (!empty($imageSize)) {
                    $_width = $imageSize[0];
                    $_height = $imageSize[1];
                }
            }

            $userValue = [
                'type' => $fileInfo['type'],
                'title' => $fileInfo['name'],
                'quote_path' => $this->quotePath . $filePath,
                'order_path' => $this->orderPath . $filePath,
                'fullpath' => $fileFullPath,
                'size' => $fileInfo['size'],
                'width' => $_width,
                'height' => $_height,
                'secret_key' => substr($fileHash, 0, 20),
            ];
        } elseif ($upload->getErrors()) {
            $errors = $this->getValidatorErrors($upload->getErrors(), $fileInfo, $option);

            if (count($errors) > 0) {
                throw new LocalizedException(__(implode("\n", $errors)));
            }
        } else {
            throw new LocalizedException(
                __("The product's required option(s) weren't entered. Make sure the options are entered and try again.")
            );
        }
        return $userValue;
    }

    /**
     * Directory structure initializing
     *
     * @return void
     * @see \Magento\Catalog\Model\Product\Option\Type\File::_initFilesystem
     */
    protected function initFilesystem()
    {
        $this->mediaDirectory->create($this->path);
        $this->mediaDirectory->create($this->quotePath);
        $this->mediaDirectory->create($this->orderPath);

        // Directory listing and hotlink secure
        $path = $this->path . '/.htaccess';
        if (!$this->mediaDirectory->isFile($path)) {
            $this->mediaDirectory->writeFile($path, "Order deny,allow\nDeny from all");
        }
    }

    /**
     * Validate contents length method
     *
     * @return bool
     * @todo need correctly name
     */
    protected function validateContentLength()
    {
        return isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > $this->fileSize->getMaxFileSize();
    }
}
