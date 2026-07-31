<?php

return [
    'empty' => [
        'method' => 'Method :class:::method does not exist.',
        'attrs' => 'Missing required parameters.',
        'attr' => 'Missing required parameter :attr.',
        'property' => ':name property does not exist.',
    ],
    'request' => [
        'error' => 'Request error.',
        'noPermission' => 'You do not have permission to access this resource.',
    ],
    'data' => [
        'typeError' => 'Wrong data type. It must be a closure or an array.',
    ],
    'route' => [
        'miss' => 'The requested address does not exist.',
    ],
    'upload' => [
        'filesizeRrror' => 'The uploaded file size exceeds the system limit.',
        'fileExtError' => 'This file format is not supported.',
        'fileMineError' => 'This file format is not supported.',
        'succ' => 'Upload successful.',
        'fail' => 'Upload failed.',
        'noPermission' => 'You do not have permission to upload files.',
    ],
    'insert' => [
        'succ' => 'Added successfully.',
        'fail' => 'Add failed.',
        'exists' => 'The same data already exists. Please do not add it again.',
    ],
    'delete' => [
        'succ' => 'Deleted successfully.',
        'fail' => 'Delete failed.',
    ],
    'update' => [
        'succ' => 'Updated successfully.',
        'fail' => 'Update failed.',
    ],
    'query' => [
        'succ' => 'Query successful.',
        'fail' => 'Query failed.',
    ],
    'operation' => [
        'succ' => 'Operation successful.',
        'fail' => 'Operation failed.',
        'noPermission' => 'You do not have permission to perform this operation.',
        'exists' => 'Please do not repeat this operation.',
        'noExists' => 'The selected record does not exist.',
    ],
    'not' => [
        'exist' => ':attr does not exist.',
    ],
];