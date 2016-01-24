# Rule engine
Stateless rule engine to assert statements given a context.

# Install
```bash
$ composer require "subzeta/ruling":"dev-master"
```

# Usage example
```php

// Composer install
require '/path/to/vendor/autoload.php';

// Non-composer install
require '/path/to/src/subzeta/Ruling/Autoloader.php';

use subzeta\Ruling\Ruling;

$my = new \stdClass();
$my->sensitivity = 80;
$my->joyfulness = 10;

$ruling = new Ruling();

$ruling
    ->given([
        'sensitivity' => $my->sensitivity,
        'joyfulness' => $my->joyfulness
    ])->when(
        ':sensitivity is greater than 90 or :joyfulness is less than 20'
    )->then(function() {
        echo 'Hell yeah, I should listen it!';
    })->otherwise(function() {
        echo 'I\'m happy enough right now, thanks.';
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
When the provided rules isn't valid (accepts: 'string' or ['string-1', ..., 'string-x']) 
#### InvalidCallbackException
When the provided success/fail callback isn't callable (accepts: function(){return 'Hey Ho! Let\'s go!';})

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
Logical | or | \|\|

# Notes
* It's not necessary to provide callbacks for *execute* method, it will return a boolean instead as *assert* does.
* Rules respect the operator precedence and evaluate the parenthesis from right to left.

# Testing
```bash
$ phpunit
```

# To do
* Increase the number of unit tests to prevent bad formatted rules from being executed.
* Add more operators (in, for example).
* Allow aliases ("is equal to" can be written as "is" and "is not equal to" as "is not"/"isn't").
* Context values may permit callable functions too.
* It can be interesting to implement a kind of *dump* method to show the interpreted rule.
