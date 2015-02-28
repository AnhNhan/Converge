Application Transactions
========================

Note: This document does not specify the current implementation, but the future
      targeted architecture and implementation.

```
Blog Post
    author: Bob
    title: Hello, Wolrd!
    text: Some random text.
    version: 1

=> record a changeset
XactSet
    actor: Alice
    oldVersion: 1
    newVersion: 2
    xacts:
        - Xact
            type: change.title
            oldValue: "Hello, Wolrd!"
            newValue: "Hello, World!"
        - Xact
            type: change.text
            oldValue: "Some random text."
            newValue: "Some proper text."

=> results in
Blog Post
    author: Bob
    title: Hello, World!
    text: Some proper text.
    version: 2
```

Reasoning
=========

To enable

 * tracable history of interactions
 * record keeping of changes

in an efficient manner that does not kill our developers.

Some nice things we can do with it
==================================

 * feed into activity stream & subscription recording
 * diff objects
 * revert changes

Some special cases
==================

Marking irrelevancy + inappropriateness
---------------------------------------

These kind of transactions are stored with the object, not on the transaction itself.
