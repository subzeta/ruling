# Rule engine
Stateless rule engine to assert statements given a context.

# Install
```bash
$ composer require "subzeta/ruling":"dev-master"
```

# Usage example
```php

require '/path/to/vendor/autoload.php';

use subzeta\Ruling\Ruling;

$my = new \stdClass();
$my->sensitivity = 80;
$my->joyfulness = 10;

(new Ruling())
    // 1-. the context
    ->given([
        'sensitivity' => $my->sensitivity,
        'joyfulness' => $my->joyfulness
    // 2-. the rules
    ])->when(
        ':sensitivity is greater than 90 or :joyfulness is less than 20'
    // 3-. the optional success callback
    )->then(function() {
        echo 'Hell yeah, I should listen music right now!';
    // 4-. the optional fail callback
    })->otherwise(function() {
        echo 'I\'m happy enough, thanks.';
    // 5-. run!
    })->execute();

// Outputs: Hell yeah, I should listen it!
```

# Calls
There are two main entrances to evaluate a given context:
#### assert
Returns a boolean indicating the output.
#### execute
Fires the success or fail callback if defined.

# Error handling
Different types of exceptions are thrown when something goes wrong:
#### InvalidContextException
When the provided context isn't valid (accepts: ['string-key-1' => value-1, ..., 'string-key-x' => value-x]).
#### InvalidRuleException
When the provided rules aren't valid (accepts: 'string' or ['string-1', ..., 'string-x']) 
#### InvalidCallbackException
When the provided success/fail callback isn't callable (accepts: function(){return 'Hey Ho! Let\'s go!';})

# Supported Operators
Type | Operator | Representation
---- | -------- | -----------
Comparison | is greater than | >
Comparison | is greater or equal to | >=
Comparison | is less than | <
Comparison | is less or equal to | <=
Comparison | is equal to (alias: is) | ==
Comparison | is not equal to (aliases: is not, isn't) | !=
Comparison | same as | ===
Comparison | not same as | !==
Logical | and | &&
Logical | or | \|\|

# Notes
* It's not necessary to provide callbacks for *execute* method, it will return a boolean instead as *assert* does.
* Rules respect the operator precedence and evaluate the parenthesis from right to left.

# Testing
```bash
$ phpunit
```

# Recursive to do list
* Increase the number of unit tests to prevent bad contexts or bad formatted rules from being executed.

# To do
* Add more operators (in, for example).
* It can be interesting to implement a kind of *dump* method to show the interpreted rule.

# Changelist
* Allow aliases ("is equal to" can be written as "is" and "is not equal to" as "is not"/"isn't").
* Context values may permit callable functions too.
* Added the strict comparison operators (same as, not same as).