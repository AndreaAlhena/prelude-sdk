# API Docs
1. Always refer to the official API docs https://docs.prelude.so
2. Prepare an usage example for each API method
3. When a new feature is added, prepare a test suite
4. When a feature is removed, be sure to remove the unuseful related tests
5. When a feature is modified, update the test suite accordingly
6. Run tests before and after the change to ensure everything works as expected

## Api Docs Verify
1. Verify API is documented here https://docs.prelude.so/verify/v2/documentation/introduction
2. Verify API create method is documented here https://docs.prelude.so/verify/v2/api-reference/create-or-retry-a-verification
3. Verify API check method is documented here https://docs.prelude.so/verify/v2/api-reference/check-a-code

## Api Docs Transactional
1. Transactional API is documented here https://docs.prelude.so/transactional/v2/api-reference/send-a-transactional-message

## Api Docs Lookup
1. Lookup API is documented here https://docs.prelude.so/lookup/v2/documentation/introduction
2. Lookup API Lookup a number is documented here https://docs.prelude.so/lookup/v2/api-reference/lookup-a-number

## Api Docs Watch
1. Watch API Predict Outcome is documented here https://docs.prelude.so/watch/v2/api-reference/predict-outcome
2. Watch API Send Feedbacks is documented here https://docs.prelude.so/watch/v2/api-reference/send-feedbacks
3. Watch API Dispatch Events is documented here https://docs.prelude.so/watch/v2/api-reference/dispatch-events

# Coding style
1. Always separate declarative blocks from imperative blocks
2. Always use strict typing

## Properties and Methods naming conventions
1. Private properties and methods should always be prefixed with an underscore
2. Properties and methods should always be sorted alphabetically
3. Properties that are optional should have a proper default value. In example, null for objects, empty string for strings and 0 for numbers (when possible)

## Arrays
1. Sort associative array keys in alphabetical order

## Import statements
1. Always remove unused import statements
2. Sort import statements alphabetically
3. Group import statements by type (e.g. core PHP, external libraries, internal classes)
4. Group import statements by namespace (e.g. Symfony components, Laravel facades, custom classes)