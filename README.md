# Rule engine
Stateless rule engine to assert statements given a context.

# Install
```bash
$ composer require "subzeta/ruling":"dev-master"
```

# Usage example
```php
<?php

namespace mynamespace\Http\Controllers;

use subzeta\Ruling\Ruling;

class MusicController extends Controller
{
    public function shouldIListenMusic()
    {
        $me = new \stdClass();
        $me->sensitivity = 80;
        $me->joyfulness = 10;

        try {
            (new Ruling())
                ->given([
                    'sensitivity' => $me->sensitivity,
                    'joyfulness' => $me->joyfulness
                ])->when(
                    ':sensitivity is greater than 90 or :joyfulness is less than 20'
                )->then(function() {
                    echo 'Hell yeah, I should listen it';
                })->otherwise(function() {
                    echo 'I\'m happy enough right now, thanks.';
                })->execute();

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
```

# Calls
There are two main entrances to assert a given context:
#### evaluate
Returns a boolean indicating the output.
#### execute
Fires the success/fail callback if they were provided, if they weren't  returns a boolean as the *evaluate* method does.

# Error handling
Different types of exceptions are thrown when something goes wrong:
#### InvalidContextException
#### InvalidRuleException
#### InvalidCallbackException

# Supported Operators
Type | Operator | Representation
---- | -------- | -----------
Comparison | is greater than | >
Comparison | is greater or equal to | >=
Comparison | is less than | <
Comparison | is less or equal to | <=
Comparison | is equal to | ==
Comparison | is not equal to | !=
Logical | and | &&
Logical | or | ||

# Testing
```bash
$ phpunit
```
