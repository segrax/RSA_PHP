#Rsa_PHP

A quickly put together PHP only (requires the BCMath extension) RSA implementation, it works... but has limitations (such as the size of the Primes used)

####Testing
```
php rsa.php
Found Primes: 17891 and 22367

P: 17891
Q: 22367
N: 400167997
E: 17
D: 211832333
Totient: 400127740

Encrypting: AAAZXZZZZZZZZZZZZZZZZZB
Encrypted (base64): uQq6F7kKuhe5CroXgGFXBnr6NA2AYVcGgGFXBoBhVwaAYVcGgGFXBoBhVwaAYVcGgGFXBoBhVwaAYVcGgGFXBoBhVwaAYVcGgGFXBoBhVwaAYVcGgGFXBp4DahM=
Decrypted: AAAZXZZZZZZZZZZZZZZZZZB

```