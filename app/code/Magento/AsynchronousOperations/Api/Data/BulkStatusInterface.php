<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AsynchronousOperations\Api\Data;

/**
 * Interface BulkStatusInterface
 *
 * @api
 */
interface BulkStatusInterface extends BulkSummaryInterface
{

    const OPERATIONS_LIST = 'operations_list';

    /**
     * Retrieve list of operation with statuses (short data).
     *
     * @return \Magento\AsynchronousOperations\Api\Data\OperationStatusInterface[]
     */
    public function getOperationsList();

    /**
     * Set operations list.
     *
     * @param \Magento\AsynchronousOperations\Api\Data\OperationStatusInterface[] $operationStatusList
     * @return $this
     */
    public function setOperationsList($operationStatusList);
}
