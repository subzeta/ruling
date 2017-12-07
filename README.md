# Rule engine
Stateless rule engine to assert statements given a context.

# Install
```bash
$ composer require "subzeta/ruling" : "^2.0.0"
```

# Usage example
```php

require '/path/to/vendor/autoload.php';

use subzeta\Ruling\Ruling;

$my = new \stdClass();
$my->sensitivity = 80;
$my->joyfulness = 10;

(new Ruling())
    ->given([
        'sensitivity' => $my->sensitivity,
        'joyfulness' => $my->joyfulness
    ])->when(
        ':sensitivity is greater than 90 or :joyfulness is less than 20'
    )->then(function() {
        echo 'Hell yeah, I should listen music right now!';
    })->otherwise(function() {
        echo 'I\'m happy enough, thanks.';
    })->execute();

// Outputs: Hell yeah, I should listen music right now!
```

# Calls
There are three main entrances:
#### interpret
Returns the interpreted rules.
Using the example above the output would be: ['80 > 90 || 10 < 20']
#### assert
Returns a boolean indicating the output.
Using the example above the output would be: true
#### execute
Fires the success or fail callback if defined.
Using the example above the output would be: 'Hell yeah, I should listen it!'

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
Containment | contained in (alias: in) | in 

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
* Improve the interpreted method response. 

# Changelist
* Allow aliases ("is equal to" can be written as "is" and "is not equal to" as "is not"/"isn't").
* Context values may permit callable functions too.
* Added the strict comparison operators (same as, not same as).
* It can be interesting to implement a kind of *dump* method to show the interpreted rule.
* Added the "in" operator.
* Context accepts array values.

# License
MIT