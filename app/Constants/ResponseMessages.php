<?php

namespace App\Constants;

class ResponseMessages
{
    // رسائل العمليات القياسية
    public const INDEX_SUCCESS = 'Data retrieved successfully.';
    public const SHOW_SUCCESS = 'Data retrieved successfully.';
    public const CREATE_SUCCESS = 'Record created successfully.';
    public const STORE_SUCCESS = 'Data stored successfully.';
    public const EDIT_SUCCESS = 'Edit operation completed successfully.';
    public const UPDATE_SUCCESS = 'Data updated successfully.';
    public const DELETE_SUCCESS = 'Record deleted successfully.';

    // رسائل العمليات العامة
    public const GENERAL_SUCCESS = 'Operation completed successfully.';
    public const GENERAL_FAILURE = 'Operation failed. Please try again later.';

    public const NOT_FOUND = 'Ops not found!';

    public const VALIDATION_FAILURE = 'Validation failed.';

    public const UNAUTHORIZED = 'Email or password incorcet.';
}
