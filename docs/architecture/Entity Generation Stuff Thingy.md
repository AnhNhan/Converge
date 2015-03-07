
Stuff to be done later
======================

* Have transaction editors generated
* Have transaction values/entities generated
* Type checking
* Built-in stuff

Up for consideration
====================

* Macro/Generated stuff, e.g. generating transaction entities from entities

Specs (is it?)
==============

General
-------

```
struct User is TransactionAwareEntity<UserTransaction>
{
    unique uid: UniqueId<"USER"> // Built-in type, infer UID parameters from parameters
    username // Simple field, type defaults to String/VARCHAR(255)
    unique username_canon // Unique constraint

    // Collection type denotes "Has many" relationship. Which one depends on how it is reversed.
    credentials: Collection<UserCredentialSet>

    roles: Collection<Role>
    emails: Collection<Email>
}
```

Behaviors
---------

* All fields have a getter method of the same name (field `author` has method `author()`)
  * We can specify the requirement for fields in interfaces
* Mutable fields receive a setter methods
* Setter methods return `$this` to enable method chaining
* Immutable fields get initialized in the constructor
* Fields annotated with `auto_init` do not initialize from a constructor value, but instead get initialized with a value appropriate for their type (e.g. a field of type `DateTime` gets initialized with the expression `new \DateTime` in PHP)
