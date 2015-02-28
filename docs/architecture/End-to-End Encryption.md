End-to-End Encryption
=====================

Data Models
===========

Encrypted data fields
---------------------

Ceylon: Annotated with `encrypted`.
PHP: Uhh...

Data payload (API)
```json
{
    "fieldName": "description",
    "encryptionKey": "workspaceKey('WORKSPACE-adoigjbwe9i3onfe', 'default')",
    // or
    "encryptionKey": {
        "type": "workspaceKey",
        "subject": "WORKSPACE-adoigjbwe9i3onfe",
        "keyName": "default"
    },
    "payload": "<lots of binary data in base64>"
}
```

Code model
```ceylon
interface EncryptionKey
{
    shared formal Array<Byte> key;
    shared formal String name;
    shared formal Workspace|Organization|User keyHolder;

    shared formal Boolean invalidated;
}

interface EncryptionType
    of None
        | KeyEncrypted
{}

"Denotes that the payload is encrypted, using the described key."
interface KeyEncrypted
    of InstallationKey // Key shared across the deployment (all servers) <-- is this ever used?
        | WorkspaceKey // Workspace-specific key (probably not saved anywhere)
        | OrganizationKey // Key of a group or organization (one, or do we allow more?)
        | UserKey // Key of a single user (one, or do we allow more?)
    satisfies EncryptionType
{
    // Actually uid?
    // A: Stuff is usually in another database, so yes. Gets decided by the planned ORM, though.
    shared formal
    Workspace|Organization|User keyHolder;

    shared formal
    String keyName;
}

interface None of none {}
object none satisfies None {}

interface WorkspaceKey
{
    shared actual
    Workspace keyHolder;
}
// ...

interface EncryptedField<T = String>
{
    shared formal
    String fieldName;

    shared formal
    EncryptionType encryptionParameter;

    shared formal
    String payload;

    shared default
    T(String) transform => identity<T>;

    // Not to be used on the server-side
    shared formal throws(`WrongKeyException`, "when you supplied a non-fitting key.")
    T unencrypted(EncryptionKey? encryptionKey)
    {
        switch (encryptionParameter)
        case (is None)
        {
            return transform(payload);
        }
        case (is WorkspaceKey | InstallationKey | OrganizationKey | UserKey)
        {
            if (encryptionParameter.keyHolder == encryptionKey.keyHolder,
                encryptionParameter.keyName == encryptionKey.name)
            {
                return transform(decrypt(encryptionKey, payload));
            }

            throw WrongKeyException();
        }
    }
}
```

Notes:
 * Protection for encryption key in case of foreign memory read-out? (client)
    * Black-out after use? <- What if we have to use multiple times? That's like, always.
    * Encrypt?
 * What happens when somebody *does* figure out the encryption key?
    * I'd say regenerate keys.
        1. Generate new pair of encryption keys everywhere
        2. Store keys in user key sets, encrypted with user's encryption key (assuming we have them)
        3. Queue up every single data encrypted by the compromitted key for re-encryption with the new key
        4. Old data gets decrypted by clients who have the compromitted key, and re-encrypted with the new key
    * What happens when somebody gets hands on the password? They'd get *all* keys, **including the new key**. And would be able to login again.
        * Block all clients except for one / two (who are hopefully not the compromitted ones), and re-generate all credential / key sets for others?

Encrypted interactions
======================

User Authentication and Authorization
-------------------------------------

* Thanks to all this, we don't need to transmit passwords over the wire
    * So far, we transmitted the password to the server to test it against a usually hashed package, verifying it's matching what we know from the user
        * obviously, susceptible to MITM / sniffing attacks
        * and offline brute forcing in case of breaches (or the server does not rate-limit attempts)
    * We reverse it a little bit. Now, we generate a test package using the user's public encryption key, which has to be successfully decrypted by the user's private key (re-)generated from his password client-side, and the result verified with the server for it to grant an access token & other encryption keys
        * requires lots of sniffing (more sniffed attempts yields more data to verify against), and offline brute forcing (it helps that encryption keys are *way* longer than idiomatic password hashes)
        * it's just less stressful to guess to password. *way* less stress than with regular password hashing
        * Encryption key IV generated more or less solely from password
        * Uh, just use the salted (+ hashed?) user password as a seed/iv?
            * This would require us to send the salt over the wire. Free offline attacks :/
            * Another approach: Another key-exchange (:/). Server generates encryption key pair, sends encryption key to client. Client generates authentication test payload and sends using that encryption key.
            * Why don't we just use TLS?

Data retrieval by client
--------------------------

* Client sends along the access token with each request, from which we can be certain that a client is authenticated
* We do visibility checks on each single piece of data, maybe hiding it from the server response
* We do de-normalization as far as possible (we may or may not encrypt associations, too. those would get resolved on the client. lovely n+1 :))

Data operations
---------------

* Client sends back maybe-encrypted JSON data (similar to how he received it)
* How can we ascertain that clients do not send in (encrypted) mal-formed data?
  * We can only really check for non-encrypted data
  * How to verify clients? API keys won't be enough, client code may be tampered, too

Data transfer
-------------

* Additionally do HTTPS? Just to be safe.
  * Client can be certain about authenticity of connection
