<?php
namespace TestHelpers;

// A test-specific version of file_get_contents
// This allows us to mock the php://input stream
function file_get_contents($filename) {
    if ($filename === 'php://input') {
        global $mock_input_stream;
        if (isset($mock_input_stream)) {
            return stream_get_contents($mock_input_stream);
        }
    }
    return \file_get_contents($filename);
}
