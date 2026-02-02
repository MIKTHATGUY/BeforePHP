<?php
// This page deliberately throws an error to test the error boundary
throw new Exception("This is a test error! The error boundary should catch this.");
