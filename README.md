# Psical Expiring PHP links

Using shared hosting (Apache + PHP), this project produces links under a protected resource that expires after a certain time period.
When using a link (psical.co.za/q?l=<AES_ENCRYPTED_LINK>), the server decrypts the "l" field and returns the HTML content the link is pointing to. HTML content is protected, and is only accessed via the encrypted link that contains an expiry time.