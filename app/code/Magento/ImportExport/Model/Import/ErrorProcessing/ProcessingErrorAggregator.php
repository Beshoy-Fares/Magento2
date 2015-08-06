<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ImportExport\Model\Import\ErrorProcessing;

/**
 * Import/Export Error Aggregator class
 */
class ProcessingErrorAggregator implements ProcessingErrorAggregatorInterface
{
    /**
     * @var string
     */
    protected $validationStrategy = self::VALIDATION_STRATEGY_STOP_ON_ERROR;

    /**
     * @var int
     */
    protected $allowedErrorsCount = 0;

    /**
     * @var ProcessingError[]
     */
    protected $items = [];

    /**
     * @var int[]
     */
    protected $invalidRows = [];

    /**
     * @var int[]
     */
    protected $errorStatistics = [];

    /**
     * @var string[]
     */
    protected $messageTemplate = [];

    /**
     * @var \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorFactory
     */
    protected $errorFactory;

    /**
     * @param \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorFactory $errorFactory
     */
    public function __construct(
        \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorFactory $errorFactory
    ) {
        $this->errorFactory = $errorFactory;
    }

    /**
     * @param string $errorCode
     * @param string $errorLevel
     * @param int|null $rowNumber
     * @param string|null $columnName
     * @param string|null $errorMessage
     * @param string|null $errorDescription
     * @return $this
     */
    public function addError(
        $errorCode,
        $errorLevel = ProcessingError::ERROR_LEVEL_CRITICAL,
        $rowNumber = null,
        $columnName = null,
        $errorMessage = null,
        $errorDescription = null
    ) {
        $this->processErrorStatistics($errorLevel);
        $this->processInvalidRow($rowNumber);
        $errorMessage = $this->getErrorMessage($errorCode, $errorMessage, $columnName);

        /** @var ProcessingError $newError */
        $newError = $this->errorFactory->create();
        $newError->init($errorCode, $errorLevel, $rowNumber, $columnName, $errorMessage, $errorDescription);
        $this->items[] = $newError;

        return $this;
    }

    /**
     * @param int $rowNumber
     * @return $this
     */
    protected function processInvalidRow($rowNumber)
    {
        if (null !== $rowNumber) {
            $rowNumber = (int)$rowNumber;
            if (!in_array($rowNumber, $this->invalidRows)) {
                $this->invalidRows[] = $rowNumber;
            }
        }

        return $this;
    }

    /**
     * @param $code
     * @param $template
     * @return $this
     */
    public function addErrorMessageTemplate($code, $template)
    {
        $this->messageTemplate[$code] = $template;

        return $this;
    }

    /**
     * @param int $rowNumber
     * @return bool
     */
    public function isRowInvalid($rowNumber)
    {
        return in_array((int)$rowNumber, $this->invalidRows);
    }

    /**
     * @return int
     */
    public function getInvalidRowsCount()
    {
        return count($this->invalidRows);
    }

    /**
     * @param string $validationStrategy
     * @param int $allowedErrorCount
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function initValidationStrategy($validationStrategy, $allowedErrorCount = 0)
    {
        $allowedStrategy = [
            self::VALIDATION_STRATEGY_STOP_ON_ERROR,
            self::VALIDATION_STRATEGY_SKIP_ERRORS
        ];
        if (!in_array($validationStrategy, $allowedStrategy)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('ImportExport: Import Data validation - Validation strategy not found')
            );
        }
        $this->validationStrategy = $validationStrategy;
        $this->allowedErrorsCount = (int)$allowedErrorCount;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasToBeTerminated()
    {
        return $this->hasFatalExceptions() || $this->isErrorLimitExceeded();
    }

    /**
     * @return bool
     */
    public function isErrorLimitExceeded()
    {
        $isExceeded = false;
        if ($this->validationStrategy == self::VALIDATION_STRATEGY_STOP_ON_ERROR
            && $this->getErrorsCount([ProcessingError::ERROR_LEVEL_NOT_CRITICAL]) > $this->allowedErrorsCount
        ) {
            $isExceeded = true;
        }

        return $isExceeded;
    }

    /**
     * @return bool
     */
    public function hasFatalExceptions()
    {
        return (bool)$this->getErrorsCount([ProcessingError::ERROR_LEVEL_CRITICAL]);
    }


    /**
     * @return ProcessingError[]
     */
    public function getAllErrors()
    {
        return $this->items;
    }

    /**
     * @param string[] $codes
     * @return ProcessingError[]
     */
    public function getErrorsByCode(array $codes)
    {
        $result = [];
        foreach ($this->items as $error) {
            if (in_array($error->getErrorCode(), $codes)) {
                $result[] = $error;
            }
        }

        return $result;
    }

    /**
     * @param array $errorCode
     * @param array $excludedCodes
     * @param bool $replaceCodeWithMessage
     * @return array
     */
    public function getRowsGroupedByErrorCode(
        array $errorCode = [],
        array $excludedCodes = [],
        $replaceCodeWithMessage = true
    ) {
        $result = [];
        foreach ($this->items as $error) {
            if ((!empty($errorCode) && in_array($error->getErrorCode(), $errorCode))
                || in_array($error->getErrorCode(), $excludedCodes)
            ) {
                continue;
            }
            $message = $error->getErrorMessage() && $replaceCodeWithMessage ?
                $error->getErrorMessage() : $error->getErrorCode();
            if (null !== $message) {
                if (!isset($result[$message])) {
                    $result[$message] = [];
                }
                $result[$message][] = $error->getRowNumber()+1;
            }
        }
        return $result;
    }

    /**
     * @return int
     */
    public function getAllowedErrorsCount()
    {
        return $this->allowedErrorsCount;
    }

    /**
     * @param string[] $errorLevels
     * @return int
     */
    public function getErrorsCount(
        array $errorLevels = [ProcessingError::ERROR_LEVEL_CRITICAL, ProcessingError::ERROR_LEVEL_NOT_CRITICAL]
    ) {
        $result = 0;
        foreach ($errorLevels as $errorLevel) {
            $result += isset($this->errorStatistics[$errorLevel]) ? $this->errorStatistics[$errorLevel] : 0;
        }

        return $result;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->items = [];
        $this->errorStatistics = [];
        $this->invalidRows = [];

        return $this;
    }

    /**
     * @param string $errorCode
     * @param string $errorMessage
     * @param string $columnName
     * @return string
     */
    protected function getErrorMessage($errorCode, $errorMessage, $columnName)
    {
        if (null === $errorMessage && isset($this->messageTemplate[$errorCode])) {
            $errorMessage = (string)__($this->messageTemplate[$errorCode]);
        }
        if ($columnName && $errorMessage) {
            $errorMessage = sprintf($errorMessage, $columnName);
        }
        if (!$errorMessage) {
            $errorMessage = $errorCode;
        }

        return $errorMessage;
    }

    /**
     * @param string $errorLevel
     * @return $this
     */
    protected function processErrorStatistics($errorLevel)
    {
        if (!empty($errorLevel)) {
            isset($this->errorStatistics[$errorLevel]) ?
                $this->errorStatistics[$errorLevel]++ : $this->errorStatistics[$errorLevel] = 1;
        }

        return $this;
    }
}
