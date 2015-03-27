
Stuff to be done later
======================

* Have transaction editors generated
* Have transaction values/entities generated
* Type checking
* Built-in stuff

Up for consideration
====================

* Macros / Templates / Mixins

Specs
=====

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

Function Calls
--------------

Currently only a few functions are defined. It is currently not possible to define functions in user-land.

### Top-Level Functions

There are currently two defined top-level functions:

 - `generateTransactionSet(SingleTypeSpec) : Struct`
 - `generateTransactionValue(SingleTypeSpec) : de.anhnhan.php.ast::Class`
   - This function returns a `Class` instance since it contains a few constants and specializations

Should any other functions be encountered, an error is emitted.

### Embedded Functions

Function calls embedded within structs are currently ignored by the reference implementation, since no functions are defined in an embedded context.

Behaviors
---------

* All fields have a getter method of the same name (field `author` has method `author()`)
  * We can specify the requirement for fields in interfaces
* Mutable fields receive a setter methods
* Setter methods return `$this` to enable method chaining
* Immutable fields get initialized in the constructor
* Fields annotated with `auto_init` do not initialize from a constructor value, but instead get initialized with a value appropriate for their type (e.g. a field of type `DateTime` gets initialized with the expression `new \DateTime` in PHP)

Type handling
---------------

### 'Primitive' built-in types

- **String**: `VARCHAR(255)` or equivalent
- **Integer**: `INTEGER` or equivalent (at least int32 range)
- **Text**: `LONGTEXT` or equivalent
- **Float**: `FLOAT`
- **Boolean**: Should be able to represent the usual two states. `TINYINT(1)` in MySql.
- **DateTime**: Whatever works for the DB engine + ORM (or what we use)

More types may be added if required.

### AutoId

### UniqueId

### Null

Maps to the sql `NULL` value of the respective engine, and is represented using the `null` value of the platform the application is developed in.

### Multi-Types / Union Types

Current behavior in the reference implementation just annotates them as `string` (`VARCHAR`) columns.

A more sophisticated behavior is going to be required at some time.

### Collection<Element>

Signifies a `*-to-many` entity relation with another entity.

* The attribute + getter will yield a collection type suitable on the platform used (`PersistentCollection` for PHP+Doctrine, `Iterable` for Ceylon)
* Collections cannot be annotated `#mutable`, they are by definition
* Collections cannot be unioned with another type
  * It's elements' type(s) can be, though (e.g. `Collection<User|Group>`)
* Additional methods
  * `'add' + ucfirst(field.name)`
  * `'remove' + ucfirst(field.name)`
  * `'has' + ucfirst(field.name)`

### Map<Key, Element>

Unspecified.

What are the use cases for this type?

### Enum<...>

Unspecified.

TODO: Define exact usage.

### ExternalReference<Element>

Signifies that the targeted entity is situated in a place where your joins can't reach it.

* The field itself only contains a discriminatable ID (`UniqueID`, hello)
* An attribute in the form `field.name + '_object'` will contain the actual object
* The getter method `field.name` will return the object
  * How to handle non-initialized values?
* An additional getter method `field.name + 'Id'` will return the ID

### ExternalCollection<Element>

Like `Collection<Element>`, but uses proxy objects to connect the edges instead of the objects themselves.

Considerations:

* Only surface the actual referenced elements, or surface the proxy objects?
