<?php

namespace App\Core\Enums;

enum StatusEnum: string
{
    /**
     * Common statuses
     *
     */
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case REJECTED = 'rejected';
    case PENDING = 'pending';
    case PROCESSED = 'processed';

    /**
     * File statuses
     *
     */
    case UPLOADED = 'uploaded';
    case UPLOADING = 'uploading';
    case VALIDATING = 'validating';
    case VALIDATED = 'validated';
    case ARCHIVED = 'archived';
    case PROCESSING = 'processing';
    case FAILED = 'failed';
    case DELETED = 'deleted';
    case CANCELLED = 'cancelled';
    case ROLLEDBACK = 'rolledback';
    case READY_TO_PROCESS = 'ready_to_process';

    /**
     * Product Statuses
     *
     */
    case DISCONTINUED = 'discontinued';
    case NEW = 'new';

    /**
     * Holding product statuses
     *
     */
    case APPROVED  = 'approved';
    case QUEUE     = 'queue';
}
