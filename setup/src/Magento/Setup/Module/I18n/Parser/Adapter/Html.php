<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Module\I18n\Parser\Adapter;

use Magento\Email\Model\Template\Filter;

/**
 * Html parser adapter
 */
class Html extends AbstractAdapter
{
    /**
     * Covers
     * <span><!-- ko i18n: 'Next'--><!-- /ko --></span>
     * <th class="col col-method" data-bind="i18n: 'Select Method'"></th>
     */
    const HTML_FILTER = "/i18n:\s?'(?<value>[^'\\\\]*(?:\\\\.[^'\\\\]*)*)'/i";

    /**
     * @inheritdoc
     */
    protected function _parse()
    {
        $data = file_get_contents($this->_file);
        if ($data === false) {
            throw new \Exception('Failed to load file from disk.');
        }

        $results = [];
        preg_match_all(Filter::CONSTRUCTION_PATTERN, $data, $results, PREG_SET_ORDER);
<<<<<<< HEAD
        $resultsCount = count($results);
        for ($i = 0; $i < $resultsCount; $i++) {
=======
        for ($i = 0, $count = count($results); $i < $count; $i++) {
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            if ($results[$i][1] === Filter::TRANS_DIRECTIVE_NAME) {
                $directive = [];
                if (preg_match(Filter::TRANS_DIRECTIVE_REGEX, $results[$i][2], $directive) !== 1) {
                    continue;
                }
                $quote = $directive[1];
                $this->_addPhrase($quote . $directive[2] . $quote);
            }
        }

        preg_match_all(self::HTML_FILTER, $data, $results, PREG_SET_ORDER);
<<<<<<< HEAD
        $resultsCount = count($results);
        for ($i = 0; $i < $resultsCount; $i++) {
=======
        for ($i = 0, $count = count($results); $i < $count; $i++) {
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            if (!empty($results[$i]['value'])) {
                $this->_addPhrase($results[$i]['value']);
            }
        }
    }
}
